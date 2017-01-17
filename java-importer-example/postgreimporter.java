/*
 * Disclaimer: this is just an example. The live version needs a subclass for each website to datamine from.
  * The data is never standard so it needs some work which differs from website to website, thus the need for different subclasses
  * The website to update is loaded from the database, then a csv file is downloaded and the right class is loaded to handle the new data
  * Images are downloaded if needed and resized, then stored (with imagemagick mogrify)
  * after the database is loaded, Lucene Solr will be updated and reindexed in background (no downtime for the user)
 */

package postgreimporter;

import java.io.BufferedInputStream;
import java.io.ByteArrayOutputStream;
import java.io.File;
import java.io.FileInputStream;
import java.io.FileNotFoundException;
import java.io.FileOutputStream;
import java.io.IOException;
import java.io.InputStream;
import java.io.OutputStream;
import java.io.PrintWriter;
import java.io.RandomAccessFile;
import java.io.UnsupportedEncodingException;
import java.net.MalformedURLException;
import java.net.URL;
import java.net.URLConnection;
import java.nio.file.Files;
import java.nio.file.Path;
import java.nio.file.Paths;
import java.sql.Connection;
import java.sql.DriverManager;
import java.sql.PreparedStatement;
import java.sql.ResultSet;
import java.sql.SQLException;
import java.sql.Statement;
import java.text.SimpleDateFormat;
import java.util.ArrayList;
import java.util.Collection;
import java.util.Date;
import java.util.Enumeration;
import java.util.HashMap;
import java.util.Iterator;
import java.util.List;
import java.util.Map;
import java.util.zip.GZIPInputStream;
import java.util.zip.ZipEntry;
import java.util.zip.ZipFile;

public class postgreimporter {
    
    //live Solr Settings
    private static String dbUrl = "";
    private static String dbUrl2 = "";
    private static String dbUsername = "";
    private static String dbPassword = "";
    private static String imagesPath = "";
    private static String importerPath = "";
    
    //sql
    protected static Connection connection = null;
    protected static Connection connection2 = null;
    protected static Integer myShopId;
    protected static List<String> importerLog = new ArrayList<String>();
    
    //global values for child classes
    public static List<String> allIds = new ArrayList<String>();
    protected static List<String> randomList = new ArrayList<String>();
    protected static List<String> immaginiList = new ArrayList<String>();
    protected static String allowedExt[] = {".jpg", "jpeg", "=jpg", ".gif", ".png", "n=1}", "lv=6", "y=95", "=500", "ndow", "ain$", "=600", "ium$"};
    protected static Map<String, Integer> marcheList = new HashMap<String, Integer>();
    protected static Map<String, Integer> catList = new HashMap<String, Integer>();
    protected static Map<String, Integer> coloriList = new HashMap<String, Integer>();
    protected static Map<String, Integer> taglieList = new HashMap<String, Integer>();
    protected static Map<Integer, Integer> myCatList = new HashMap<Integer, Integer>();
    protected static Map<String, Integer> singleProdsCats = new HashMap<String, Integer>();
    protected static Map<Integer, String> myCatListTag = new HashMap<Integer, String>();
    protected static Map<Integer, Collection<Integer>> catTrees = new HashMap<Integer, Collection<Integer>>();
    protected static Map<Integer, String> catTags = new HashMap<Integer, String>();
    protected static Date dateisnow = new Date();
    protected static String nowisnow = new SimpleDateFormat("yyyy-MM-dd").format(dateisnow) + "T" + new SimpleDateFormat("HH:mm:ss").format(dateisnow) + "Z";
    protected static Double eurToGBP;
    protected static Double usdToGBP;
    
    //global values for child classes
    public static String fornitore;
    public static String shipto;
    public static String myUrl;
    public static int maxJsonRows = 10000;
    public static int createThumb = 0;
    public static int ignoreMogrify = 0;
    
    public static void main(String[] args) throws InstantiationException, IllegalAccessException, ClassNotFoundException, IOException, InterruptedException, SQLException {
        try {
            //locking process (temporary removed, trying out multi updates)
            int isLocked = _lockTheProcess(); //returns 1 if already running, 0 if clear and creates lock file
            if(isLocked == 0) {
                //load All Brands and establish DB connection
                _getShopToUpdate();
                
                //set updated to the shop in the db
                _shopIsUpdated();
                
                createDirectory(imagesPath + "/" + fornitore + "/");
                
                saveLog("Loading importer." + fornitore);
                //downloading file
                if(_isgzipped(myUrl)) {
                    saveLog("File is gZipped");
                    downloadFile(myUrl, "feed.gz");
                    extract("feed.gz", "feed.csv");
                } else {
                    System.out.println(myUrl.substring(myUrl.length() - 4));
                    if(myUrl.substring(myUrl.length() - 4).equals(".zip")) {
                        unzip(myUrl, "feed.csv");
                        saveLog("File in .zip");
                    } else {
                        saveLog("Il file non è zippato");
                        downloadFile(myUrl, "feed.csv");
                    }
                }
                
                //setup values from DB
                _getDBData();
            } else {
                saveLog("processo in corso, exit");
                _removeLockFile(); //no process should run for more than ~25 min...
                return;
            }
        } catch(Exception e) {
            saveLog("Errore step 1: " + e.getMessage());
        }
        //loading class and running file parsing
        Class<?> exampleClass = Class.forName("postgreimporter." + fornitore);
        @SuppressWarnings("unused")
        Object ob = exampleClass.newInstance();
        
        try {
            //delete old and unused data
            _deleteOldData();
            
            //create Thumbnails
             if(ignoreMogrify == 0) {
                _createThumbnails();
                
                //delete tmp images
                _deleteTempImages();
             }
             
             //sending a flag to update solr
             _triggerSolrUpdate();
            
            //add log entries
            _sendLogToDB();
            
            //removing lock
            _removeLockFile();
            
            //
            _updateSolr();
        } catch(Exception e) {
            saveLog("Errore step 3: " + e.getMessage());
        }
    }
    
    private static void _updateSolr() {
        saveLog("Invio comando a Solr per l'Update");
        Process p;
        String[] cmd = {"/bin/sh", "-c", "cd /path; sudo -H -u user java -jar updatesolr.jar;"}; //solr begin the update
        try {
            p = Runtime.getRuntime().exec(cmd);
            p.waitFor();
        } catch (IOException | InterruptedException e) {
            e.printStackTrace();
        }
        saveLog("Update eseguito");
    }
    
    private static void _removeLockFile() {
        String lockPath = importerPath + "/file.lock";
        File file = new File(lockPath);
        file.delete();
    }
    
    private static int _lockTheProcess() throws IOException {
        /*String lockPath = importerPath + "/file.lock";
        File file = new File(lockPath);
        if(file.exists() && !file.isDirectory()) { 
            // do something
            return 1;
        } else {
            file.createNewFile();
            return 0;
        }*/
        return 0;
    }
    
    private static boolean _isgzipped(String myurl) throws IOException {
        URL url = new java.net.URL(myurl);

        URLConnection urlConnect;
        urlConnect = url.openConnection();
        urlConnect.setDoInput(true);
        urlConnect.setDoOutput(true);

        InputStream input = urlConnect.getInputStream();
        
        return isGZipped(input);
    }
    
    private static void _createThumbnails() throws IOException, InterruptedException {
        // Lists all files in folder
        File folder = new File(imagesPath + "/" + fornitore);
        File fList[] = folder.listFiles();
        Process p;
        // Searchs .tmp
        for (int i = 0; i < fList.length; i++) {
            File pes = fList[i];
            if (pes.getName().endsWith(".tmp")) {
                String myquality = "80";
                if(createThumb == 0) {
                    myquality = "80";
                } else {
                    myquality = "100";
                }
                String[] cmd = {"/bin/sh", "-c", "cd " + imagesPath + "/" + fornitore + "; sudo -H -u user mogrify -path " + imagesPath + "/" + fornitore + " -trim -thumbnail 'x260' -quality " + myquality + " -format jpg " + pes.getName() + ";"};
                p = Runtime.getRuntime().exec(cmd);
                p.waitFor();
            }
        }
    }
    
    private static void _deleteTempImages() {
        // Lists all files in folder
        File folder = new File(imagesPath + "/" + fornitore);
        File fList[] = folder.listFiles();
        // Searchs .tmp
        for (int i = 0; i < fList.length; i++) {
            File pes = fList[i];
            if (pes.getName().endsWith(".tmp")) {
                // and deletes
                pes.delete();
            }
        }
    }
    
    private static void _shopIsUpdated() throws SQLException {
        //removing all images and cats related to the products from the shop
        String toRemove = "DELETE FROM immagini i USING products p WHERE p.id = i.pid AND p.shop = " + myShopId + ";";
        toRemove += "DELETE FROM prod_cats c USING products p WHERE p.id = c.pid AND p.shop = " + myShopId + ";";
        PreparedStatement preparedStatement2 = connection2.prepareStatement(toRemove);
        preparedStatement2.executeUpdate();
        Statement stmtc = null;
        stmtc = connection2.createStatement();
        stmtc.executeUpdate("UPDATE shops SET lastupdate=NOW() WHERE id='" + myShopId + "'");
    }
    
    private static void _deleteOldData() throws IOException, InterruptedException, SQLException {
        //delete old data from db
        saveLog("cancello vecchi");
        String deleteOldData = "DELETE FROM products WHERE last_update < (NOW() - interval '3 hours') AND shop = " + myShopId;
        PreparedStatement ps = connection2.prepareStatement(deleteOldData);
        ps.executeUpdate();
        saveLog("faccio la nuova view");
        PreparedStatement ps2 = connection2.prepareStatement("CREATE MATERIALIZED VIEW search_indexb "
                + "AS SELECT p.id as id "
                //+ "setweight(to_tsvector(shops.language::regconfig, p.nome), 'B') || "
                //+ "setweight(to_tsvector(shops.language::regconfig, p.descrizione), 'D') || "
                //+ "setweight(to_tsvector('simple', brands.nome), 'C') || "
                //+ "setweight(to_tsvector('simple', shops.mysolrname), 'D') || "
                //+ "setweight(to_tsvector('simple', p.tags), 'A') as document "
                + "FROM products p "
                + "JOIN shops ON shops.id = p.shop "
                + "JOIN brands ON brands.id = p.marca;"
                + "DROP MATERIALIZED VIEW search_index;"
                + "ALTER MATERIALIZED VIEW search_indexb RENAME TO search_index;"
                //+ "CREATE INDEX gin_idx_document_si ON search_index USING gin(document);"
                + "CREATE INDEX id_idx_pid_si ON search_index (id);");
        ps2.executeUpdate();
        saveLog("fatta");
        //delete old unused images
        File folder = new File(imagesPath + "/" + fornitore);
        File[] listOfFiles = folder.listFiles();

        for (int i = 0; i < listOfFiles.length; i++) {
          File file = listOfFiles[i];
          if (file.isFile() && file.getName().endsWith(".jpg")) {
            if(allIds.contains(file.getName())) {
            } else {
                file.delete();
            }
          } 
        }
        
        //logging total inserted products
        saveLog("Inserted / updated a total of " + allIds.size());
    }
    
    public static void saveLog(String logEntry) {
        System.out.println(logEntry);
        importerLog.add(logEntry);
    }
    
    private static void _sendLogToDB() throws SQLException, FileNotFoundException, UnsupportedEncodingException {
        createDirectory(importerPath + "/logs/" + fornitore);
        
        PrintWriter writer = new PrintWriter(importerPath + "/logs/" + fornitore + "/log-" + nowisnow + ".txt", "UTF-8");
        for(String entry : importerLog) {
            writer.println(entry);
        }
        writer.close();
    }
    
    private static void _triggerSolrUpdate() throws SQLException {
        Statement st = connection2.createStatement();
        st.executeUpdate("UPDATE system SET update = 1 WHERE key = 'solr'");
    }
    
    public static int downloadImage(String imageS, String nome) throws IOException {
        //check if already exists
        Path path = Paths.get(imagesPath + "/" + fornitore + "/" + nome + ".jpg");

        if (Files.notExists(path)) {
            //download image
            try {
                URLConnection url = new URL(imageS).openConnection();
                url.setRequestProperty("User-Agent", "Mozilla/5.0 (Windows NT 6.1; WOW64) AppleWebKit/537.11 (KHTML, like Gecko) Chrome/23.0.1271.95 Safari/537.11");
                url.connect();
                //InputStream in = new BufferedInputStream(url.openStream());
                InputStream in = url.getInputStream();
                ByteArrayOutputStream out = new ByteArrayOutputStream();
                byte[] buf = new byte[1024];
                int n = 0;
                while (-1!=(n=in.read(buf)))
                {
                   out.write(buf, 0, n);
                }
                out.close();
                in.close();
                byte[] response = out.toByteArray();
                FileOutputStream fos = new FileOutputStream(imagesPath + "/" + fornitore + "/" + nome + ".tmp");
                fos.write(response);
                fos.close();
            } catch(Exception e){
                saveLog("Errore su download immagine per prod id: " + nome + " per errore: " + e.getMessage());
                return 0;
            }
            java.io.File pathF = new java.io.File(imagesPath + "/" + fornitore + "/" + nome + ".tmp");
            if(pathF.length() == 0) {
                saveLog("downloaded but not present OR image is 0B " + nome + ".tmp");
                return 0;
            }
        }

        return 1;
    }
    
    public static void deleteFile(String imageP) throws IOException {
        Path path = Paths.get(imageP);
        Files.deleteIfExists(path);
    }
    
    public static void createDirectory(String path) {
        File theDir = new File(path);
        
        // if the directory does not exist, create it
        if (!theDir.exists()) {
            try{
                theDir.mkdir();
            } 
            catch(SecurityException se){
                saveLog("Cannot create folder path " + path);
            }
        }

    }
    
    public static boolean extract(String filename, String csvFile) throws FileNotFoundException, IOException {
        GZIPInputStream in = new GZIPInputStream(new FileInputStream(filename));
        FileOutputStream f = new FileOutputStream(csvFile);
        
        byte[] buf = new byte[1024];
        int len;
        while((len = in.read(buf)) > 0) {
            f.write(buf, 0, len);
        }
        f.close();
        in.close();
        return true;
    }
    
    /**
      * Checks if an input stream is gzipped.
      * 
      * @param in
      * @return
      */
     public static boolean isGZipped(InputStream in) {
      if (!in.markSupported()) {
       in = new BufferedInputStream(in);
      }
      in.mark(2);
      int magic = 0;
      try {
       magic = in.read() & 0xff | ((in.read() << 8) & 0xff00);
       in.reset();
      } catch (IOException e) {
       e.printStackTrace(System.err);
       return false;
      }
      return magic == GZIPInputStream.GZIP_MAGIC;
     }
     
     /**
      * Checks if a file is gzipped.
      * 
      * @param f
      * @return
      */
     public static boolean isGZipped(File f) {
      int magic = 0;
      try {
       RandomAccessFile raf = new RandomAccessFile(f, "r");
       magic = raf.read() & 0xff | ((raf.read() << 8) & 0xff00);
       raf.close();
      } catch (Throwable e) {
       e.printStackTrace(System.err);
      }
      return magic == GZIPInputStream.GZIP_MAGIC;
     }
     
    /**
     * 
     * Working with zipped files...
     */
     public static boolean unzip(String urlInput, String filename) throws IOException {         
         downloadFile(urlInput, "feed.zip");

        try {
            ZipFile zipFile = new ZipFile("feed.zip");
            Enumeration<?> enu = zipFile.entries();
            while (enu.hasMoreElements()) {
                ZipEntry zipEntry = (ZipEntry) enu.nextElement();

                String name = filename;

                File file = new File(name);
                if (name.endsWith("/")) {
                    file.mkdirs();
                    continue;
                }

                File parent = file.getParentFile();
                if (parent != null) {
                    parent.mkdirs();
                }

                InputStream is = zipFile.getInputStream(zipEntry);
                FileOutputStream fos = new FileOutputStream(file);
                byte[] bytes = new byte[1024];
                int length;
                while ((length = is.read(bytes)) >= 0) {
                    fos.write(bytes, 0, length);
                }
                is.close();
                fos.close();

            }
            zipFile.close();
        } catch (IOException e) {
            e.printStackTrace();
        }

       return true;
    }
    
    public static boolean downloadFile(String urlInput, String filename) throws MalformedURLException {
        File f = new File(filename);
        
        URL url = new java.net.URL(urlInput);

        URLConnection urlConnect;
        try {
            urlConnect = url.openConnection();
            urlConnect.setDoInput(true);
            urlConnect.setDoOutput(true);
            byte[] buffer = new byte[8 * 1024];

            InputStream input = urlConnect.getInputStream();
            OutputStream output = new FileOutputStream(f);
            int bytesRead;
            while ((bytesRead = input.read(buffer)) != -1) {
              output.write(buffer, 0, bytesRead);
            }
            output.close();
            input.close();
        } catch (IOException e) {
            e.printStackTrace();
            return false;
        }
        
        return true;
    }
    
    protected static Integer getMarcaId(String marca) throws SQLException {
        int marca_id = 0;
        Integer value = marcheList.get(marca.toLowerCase());
        
        if(value != null) {
            marca_id = value;
        }
        
        return marca_id;
    }
    
    protected static Integer getCatId(String cat, String refid) throws SQLException {
        int cat_id = 0;
        //check if product has special assign
        Integer value1 = singleProdsCats.get(refid);
        if(value1 == null) {
            Integer value = catList.get(cat.toLowerCase());
            if(value != null) {
                cat_id = value;
            }
            if(catTrees.get(value) == null) {
                cat_id = 0;
            }
        } else {
            cat_id = value1;
        }
        return cat_id;
    }
    
    protected static Integer getColorId(String color) throws SQLException {
        Integer value = coloriList.get(color.toLowerCase());
        if(value != null) {
            return value;
        } else {
            return 0;
        }
    }
    
    @SuppressWarnings("rawtypes")
    private static void _getDBData() {
        try {
            //brands
            Statement stmt = null;
            stmt = connection.createStatement();
            ResultSet rs = stmt.executeQuery( "SELECT original, new FROM " + fornitore + "_brand;" );
            while(rs.next()) {
                int newid = rs.getInt("new");
                String original = rs.getString("original");
                marcheList.put(original.toLowerCase().replace("''", "'"), newid);
            }
            
            //categorie
            Statement stmt2 = null;
            stmt2 = connection.createStatement();
            ResultSet rs2 = stmt2.executeQuery( "SELECT original, new FROM " + fornitore + "_cat;" );
            while(rs2.next()) {
                int newid = rs2.getInt("new");
                String original = rs2.getString("original");
                catList.put(original.toLowerCase().replace("''", "'"), newid);
            }
            
            //categorie come lista
            Statement stmtc = null;
            stmtc = connection2.createStatement();
            ResultSet rsc = stmtc.executeQuery("SELECT id, parent_id, tags FROM categorie;");
            while(rsc.next()) {
                int catid = rsc.getInt("id");
                int catpid = rsc.getInt("parent_id");
                String tags = rsc.getString("tags");
                myCatList.put(catid, catpid);
                myCatListTag.put(catid, tags);
            }

            //to list
            Iterator it = myCatList.entrySet().iterator();
            while (it.hasNext()) {
                Map.Entry pair = (Map.Entry)it.next();
                Integer thisid = (Integer) pair.getKey();
                _createCatTreeFromId(thisid, thisid);
                //it.remove(); // avoids a ConcurrentModificationException
            }
            
            //to tag list
            Iterator it2 = myCatListTag.entrySet().iterator();
            while (it2.hasNext()) {
                Map.Entry p = (Map.Entry)it2.next();
                Integer thisid = (Integer) p.getKey();
                catTags.put(thisid, _createTagsList(thisid).trim());
            }
            
            //colori
            Statement stmt3 = null;
            stmt3 = connection.createStatement();
            ResultSet rs3 = stmt3.executeQuery( "SELECT original, new FROM " + fornitore + "_colori;" );
            while(rs3.next()) {
                int newid = rs3.getInt("new");
                String original = rs3.getString("original");
                coloriList.put(original.toLowerCase().replace("''", "'"), newid);
            }
            
            //taglie
            Statement stmt4 = null;
            stmt4 = connection.createStatement();
            ResultSet rs4 = stmt4.executeQuery( "SELECT original, new FROM " + fornitore + "_taglie;" );
            while(rs4.next()) {
                int newid = rs4.getInt("new");
                String original = rs4.getString("original");
                taglieList.put(original.toLowerCase().replace("''", "'"), newid);
            }
            
            //rates
            Statement stmt5 = null;
            stmt5 = connection2.createStatement();
            ResultSet rs5 = stmt5.executeQuery( "SELECT usd, eur FROM rates LIMIT 1;" );
            while(rs5.next()) {
                usdToGBP = (1 / rs5.getDouble("usd"));
                eurToGBP = (1 / rs5.getDouble("eur"));
            }
            
            //special cats for shop products
            Statement stmt6 = null;
            stmt6 = connection.createStatement();
            ResultSet rs6 = stmt6.executeQuery( "SELECT id, cat FROM assign_" + fornitore + ";" );
            while(rs6.next()) {
                singleProdsCats.put(rs6.getString("id"), rs6.getInt("cat"));
            }
        } catch (SQLException e) {
            saveLog("Connection Failed! " + e.getMessage());
            return;
        }
    }
    
    private static String _createTagsList(Integer catid) {
        String ret = "";
        //parent?
        if(myCatList.get(catid).equals(0)) {
            ret += " " + myCatListTag.get(catid);
        } else {
            //it has parents
            ret += " " + myCatListTag.get(catid);
            ret += _createTagsList(myCatList.get(catid));
        }
        
        return ret;
    }
    
    private static void _createCatTreeFromId(Integer catid, Integer originalid) {
        //loading collection
        Collection<Integer> values = catTrees.get(originalid);
        //does collection exists yet?
        if(values == null) {
            values = new ArrayList<Integer>();
            catTrees.put(originalid, values);
        }
        //adding catid to list
        values.add(catid);
        
        //parent?
        if(myCatList.get(catid).equals(0)) {
        } else {
            //it has parents
            _createCatTreeFromId(myCatList.get(catid), originalid);
        }
    }
    
    public static String getCatTags(Integer cat_id) {
        String ret = catTags.get(cat_id);
        return ret;
    }
    
    private static void _getShopToUpdate() {
        try {
            connection = DriverManager.getConnection(
                    "jdbc:postgresql:" +  dbUrl, dbUsername,
                    dbPassword);
            
            connection2 = DriverManager.getConnection(
                    "jdbc:postgresql:" +  dbUrl2, dbUsername,
                    dbPassword);
            
            Statement stmt = null;
            stmt = connection2.createStatement();
            
            ResultSet rs = stmt.executeQuery( "SELECT id, nome, url, shipto FROM shops WHERE active='true' ORDER BY lastupdate ASC LIMIT 1" );
            
            while(rs.next()) {
                int shopid = rs.getInt("id");
                String nome = rs.getString("nome");
                String urlDB = rs.getString("url");
                String shiptoDB = rs.getString("shipto");
                saveLog("NOME: " + nome);
                //assign values
                myShopId = shopid;
                fornitore = nome;
                myUrl = urlDB;
                shipto = shiptoDB;
            }
        } catch (SQLException e) {
            saveLog("Connection Failed! " + e.getMessage());
            return;
        }
    }
}
