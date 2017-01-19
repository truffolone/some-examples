/*
 * Keep in mind this is an example. In the real project all the database code was into specific classes as well as the email html templating (removed here).
 * 
 */

package sendmail;

import java.io.BufferedReader;
import java.io.FileReader;
import java.io.IOException;
import java.nio.file.Files;
import java.nio.file.Path;
import java.nio.file.Paths;
import java.sql.Connection;
import java.sql.DriverManager;
import java.sql.ResultSet;
import java.sql.SQLException;
import java.sql.Statement;
import java.util.ArrayList;
import java.util.List;
import java.util.Properties;
import javax.mail.*;
import javax.mail.internet.*;

public class sendmail {
    private static String dbUrl = "";
    private static String dbUsername = "";
    private static String dbPassword = "";
    public static String emailsToSendPath = "";
    
    private static List<String> deleteList = new ArrayList<String>();
    
    private static Connection connection = null;
    
    static final String FROM = "from@example.com";   
    static String TO = "success@simulator.amazonses.com";  //sending to amazon SES sendbox if not specified (should not happen...)
    
    static String BODY = "Default Body"; //default values
    static String SUBJECT = "Default Title"; //still default values
    
    // SMTP credentials for Amazon SES
    static final String SMTP_USERNAME = "Username";
    static final String SMTP_PASSWORD = "Password";
    
    // Amazon SES SMTP host name (here is for EUW)
    static final String HOST = "email-smtp.eu-west-1.amazonaws.com";    
    
    // Port we will connect to on the Amazon SES SMTP endpoint. We are choosing port 25 because we will use
    // STARTTLS to encrypt the connection.
    static final int PORT = 25;

    
    public static void main(String[] args) throws IOException, SQLException, MessagingException {
        //connecting to db
        _dbConnect();
        
        //sending emails
        _sendEmails();
        
        //delete from file system and database
        _deleteSent();
    }
    
    /*
     * Delete the sent emails from the filesystem
     * Returns Void
     */
    private static void _deleteSent() throws IOException, SQLException {
        //create where in list start
        String wherein = "";
        for(int i = 0; i < deleteList.size(); i++) {
            //deleting the file
            Path path = Paths.get(emailsToSendPath + deleteList.get(i) + ".html");
            Files.delete(path);
            
            //adding to wherein
            if(i > 0) {
                wherein += ",";
            }
            wherein += "'" + deleteList.get(i) + "'";
        }
        
        //deleting from database
        Statement stmt = null;
        stmt = connection.createStatement();
        String query = "DELETE FROM emails WHERE uuid IN(" + wherein + ")";
        //System.out.println(query);
        stmt.executeUpdate( query );
    }
    
    /*
     * Setup a single email send
     * Returns Void
     */
    private static void _ses() throws MessagingException {
        // Create a Properties object to contain connection configuration information.
        Properties props = System.getProperties();
        props.put("mail.transport.protocol", "smtp");
        props.put("mail.smtp.port", PORT); 
        
        // Set properties indicating that we want to use STARTTLS to encrypt the connection.
        // The SMTP session will begin on an unencrypted connection, and then the client
        // will issue a STARTTLS command to upgrade to an encrypted connection.
        props.put("mail.smtp.auth", "true");
        props.put("mail.smtp.starttls.enable", "true");
        props.put("mail.smtp.starttls.required", "true");

        // Create a Session object to represent a mail session with the specified properties. 
        Session session = Session.getDefaultInstance(props);

        // Create a message with the specified information. 
        MimeMessage msg = new MimeMessage(session);
        msg.setFrom(new InternetAddress(FROM));
        msg.setRecipient(Message.RecipientType.TO, new InternetAddress(TO));
        msg.setSubject(SUBJECT);
        msg.setContent(BODY,"text/html");
            
        // Create a transport.        
        Transport transport = session.getTransport();
                    
        // Send the message.
        try
        {
            // Connect to Amazon SES using the SMTP username and password you specified above.
            transport.connect(HOST, SMTP_USERNAME, SMTP_PASSWORD);
            
            // Send the email.
            transport.sendMessage(msg, msg.getAllRecipients());
            //System.out.println("Email sent!");
        }
        catch (Exception ex) {
            //System.out.println("The email was not sent.");
            //System.out.println("Error message: " + ex.getMessage());
        }
        finally
        {
            // Close and terminate the connection.
            transport.close();          
        }
    }
    
    /*
     * Load the emails to send (100 at time) and then calls _ses() to process the send through Amazon SES
     * Returns Void
     */
    private static void _sendEmails() throws SQLException, MessagingException, IOException {
        Statement stmt = null;
        stmt = connection.createStatement();
        String query = "SELECT * FROM emails ORDER BY created ASC LIMIT 100";
        ResultSet rs = stmt.executeQuery( query );
        //Integer con = 1; //debug only
        while(rs.next()) {
            //this is for debug only
            /*if(con > 0) {
                continue;
            }*/
            //load html email file
            String body = readFile(rs.getString("uuid") + ".html");
            
            //configure email settings
            TO = rs.getString("email");
            BODY = body;
            SUBJECT = rs.getString("title");
            
            //stream emails to amazon SES
            _ses();
            
            //adding to delete list
            deleteList.add(rs.getString("uuid"));
            
            //debug only
            //con = con + 1;
        }
    }
    
    public static String readFile(String fileName) throws IOException {
        String filePath = emailsToSendPath + fileName;
        BufferedReader br = new BufferedReader(new FileReader(filePath));
        try {
            StringBuilder sb = new StringBuilder();
            String line = br.readLine();

            while (line != null) {
                sb.append(line);
                sb.append("\n");
                line = br.readLine();
            }
            return sb.toString();
        } finally {
            br.close();
        }
    }
    
    private static void _dbConnect() {
        try {
            connection = DriverManager.getConnection(
                    "jdbc:postgresql:" +  dbUrl, dbUsername,
                    dbPassword);
        } catch (SQLException e) {
            System.out.println("Connection Failed! Check output console");
            e.printStackTrace();
            return;
        }
    }
}
