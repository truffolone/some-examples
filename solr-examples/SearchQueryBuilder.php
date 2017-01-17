<?php 

/*
 * Solr query builder. Framework used: Codeigniter 3.1
 * The controller initialise the variable we need from the user input and then creates the solr query
 * returns the json data with the products and some more informations (product in wishlist for example) based on postgresql data
 */

class SearchQueryBuilder {
    //solr settings
    private $_solrUsername = "";
    private $_solrPassword = "";
    private $_solrUrl = "";
    private $_solrPort = "";
    private $_solrCluster = "prods";
    private $_solrFormat = "json";

    //WL
    private $wishlist = array();

    //default settings
    private $perpage = 48;
    private $min_s = 0;
    private $max_s = 100;
    private $budget = 0;
    private $lbudget = 0;
    private $page = 1;
    private $forced = FALSE;
    private $sortBy = "p.first_insert";
    private $sortDir = "desc";
    private $curr = "GBP";
    private $ship_to = NULL;
    private $related = TRUE;
    
    //pre-loaded vars
    private $path = "";
    private $query = "";
    private $status = "";
    private $data = "";
    private $q = "";
    private $brands = array();
    private $colors = array();
    private $results = 0;
    private $cats = array();
    private $nocats = array();
    private $groupCats = FALSE;
    
    //security
    private $solrSortList = array(
            'score' => 'score',
            'p.sconto' => 'sconto',
            'p.prezzos' => 'prezzo',
            'brands.nome' => 'marca_sort',
            'p.first_insert' => 'random'
        );
    private $shipAllowed = array(
                    "be",
                    "fi",
                    "at",
                    "uk",
                    "it",
                    "fr",
                    "es",
                    "de",
                    "00"
        );
    private $currAvailable = array(
                    'gbp',
                    'eur',
                    'usd'
        );

    public function __construct() {
        $this->obj =& get_instance();
    }

    /*
     * Setters and Getters
     */
    public function get($value) {
        return $this->$value;
    }

    public function set($key, $value) {
        $this->$key = $value;
        return $this;
    }

    public function getQuery() {
        return $this->_myquery;
    }

    /*
     * Query setup
     */

    public function setWishlist($obj) {
        if($obj->num_rows() > 0) {
            foreach($obj->result() as $r) {
                $this->wishlist[] = $r->productid;
            }
        }
    }

    public function sortBy($sb) {
        if(array_key_exists($sb, $this->sortList)) {
            $this->sortBy = $this->sortList[$sb];
        } else {
            //revert to default
            if($this->q == "" || $this->q == "*") {
                $this->sortBy = $this->sortList['newest'];
            } else {
                $this->sortBy = $this->sortList['score'];
            }
        }

        if($this->sortBy == 'score' && ($this->q == "" || $this->q == "*")) {
            $this->sortBy = $this->sortList['newest'];
        }

        return $this;
    }

    public function sortDir($sd) {
        if($sd != "desc" && $sd != "asc") {
            $this->sortDir = "asc";
        } else {
            $this->sortDir = $sd;
        }
        return $this;
    }

    public function addCat($cid) {
        $this->cats[] = $cid;
    }

    public function addNoCat($cid) {
        $this->nocats[] = $cid;
    }

    public function setShipTo($shipto) {
        if(in_array($shipto, $this->shipAllowed)) {
            $this->ship_to = $shipto;
        } else {
            $this->ship_to = "all";
        }
        return $this;
    }

    public function addBrand($brand_id) {
        if(is_numeric($brand_id)) {
            $this->brands[] = $brand_id;
        }
        return $this;
    }

    public function addColor($color) {
        $this->colors[] = $color;
        return $this;
    }

    public function setCurrency($curr) {
        if(in_array($curr, $this->currAvailable)) {
            $this->curr = $curr;
        }
    }

    public function searchQuerySolr() {
        //solrUrl
        $url = "http://" . $this->_solrUsername . ":" . $this->_solrPassword . "@" . $this->_solrUrl . ":" . $this->_solrPort . "/solr/" . $this->_solrCluster . "/select";

        //currency
        $currency = strtoupper($this->curr);
        //query
        if($this->q != "" && $this->q != "*") {
            $textqarr = array();
            $qa = explode(" ", $this->q);
            foreach($qa as $qqq) {
                $textqarr[] = $qqq;
            }
            $qq = urlencode(implode(" ", $textqarr));
            $qWhere = $qq;
            $boost = "nome^2.5 marcanome^3 shopnome^2 tags^3.5";
            $url .= "?q=" . $qWhere . "&defType=dismax&qf=" . urlencode($boost) . "&qs=2&mm=" . urlencode("2<75%");
        } else {
            $qWhere = "*";
            $url .= "?q=" . $qWhere;
        }
        //building cats where
        $cats = "categoria:(" . implode(" ", $this->cats) . "";
        //nocats
        $nocatsWhere = "";
        if(count($this->nocats) > 0) {
            $nocatsWhere = " -" . implode(" -", $this->nocats);
        }
        $url .= "&fq=" . urlencode($cats . $nocatsWhere . ")");
        //brands
        if(count($this->brands) > 0) {
            $bWhere = "&fq=" . urlencode("marcaid:(" . implode(' ', $this->brands) . ")");
        } else {
            $bWhere = "";
        }
        $url .= $bWhere;
        //colors
        if(count($this->colors) > 0) {
            $cWhere = "&fq=" . urlencode("coloreid:(" . implode(' ', $this->colors) . ")");
        } else {
            $cWhere = "";
        }
        $url .= $cWhere;
        //budget
        if($this->budget > 0 && $this->budget != 9999999) {
            $budgetW = $this->budget . "," . $currency;
        } else {
            $budgetW = "*";
        }
        //lbudget
        if($this->lbudget > 0 && $this->lbudget != "") {
            $lbudgetW = $this->lbudget . "," . $currency;
        } else {
            $lbudgetW = "*";
        }
        $url .= "&fq=" . urlencode("prezzos:[" . $lbudgetW . " TO " . $budgetW . "]");
        //min_s
        if($this->min_s > 0 && $this->min_s < 100) {
            $min_sW = $this->min_s;
        } else {
            $min_sW = "0";
        }
        //max_s
        if($this->max_s > 0 && $this->max_s < 100) {
            $max_sW = $this->max_s;
        } else {
            $max_sW = "100";
        }
        $url .= "&fq=" . urlencode("sconto:[" . $min_sW . " TO " . $max_sW . "]");
        //shipto
        if($this->ship_to != "" && $this->ship_to != "00") {
            $shiptolimit = urlencode($this->ship_to . " all");
        } else {
            $shiptolimit = "all";
        }
        $url .= "&fq=shipto:(" . $shiptolimit . ")";
        //sorting
        $sort = $this->solrSortList[$this->sortBy];
        if($sort == 'random' || $sort == '') {
            $sort = 'random_' . md5(date('YMDH'));
        }
        if($sort == 'score'){
            $url .= "&sort=" . urlencode($sort . " " . $this->sortDir . ", random_" . md5(date('YMDH')) . " desc");
        } else {
            $url .= "&sort=" . urlencode($sort . " " . $this->sortDir);
        }
        //page
        $url .= "&start=" . ($this->page -1) * $this->perpage . "&rows=" . $this->perpage;
        //coding
        $url .= "&wt=json";
        //fields
        $url .= "&fl=" . urlencode("id,lowershop,shopnome,nome,prezzos:currency(prezzos," . $currency . "),prezzo:currency(prezzo," . $currency . "),sconto,marcanome,immagine");

        $json = json_decode(file_get_contents($url));

        $return = array();
        $return['products'] = array();
        foreach($json->response->docs as $result) {
            $return['products'][] = array(
                'id' => $result->id,
                'shop' => $result->shopnome,
                'lowershop' => $result->lowershop,
                'nome' => $result->nome,
                'prezzos' => $result->prezzos,
                'prezzo' => $result->prezzo,
                'sconto' => $result->sconto,
                'marca' => $result->marcanome,
                'wishlist' => in_array($result->id, $this->wishlist) ? 1 : 0,
                'mainimg' => $result->immagine
            );
        }

        $return['numFound'] = $json->response->numFound;

        return $return;
    }

    public function searchQuerySolrSelection($selid) {
        //getting ids
        $re = $this->obj->db->select("pid")->where("affselid", $selid)->get("aff_sel_prods");
        $allProds = array();
        if($re->num_rows() > 0) {
            foreach($re->result() as $rr) {
                $allProds[] = $rr->pid;
            }
            $idlist = urlencode("id:(" . implode(" " , $allProds) . ")");
        } else {
            $idlist = urlencode("id:(77hhaui)"); //fake to find 0 items
        }
        //solrUrl
        $url = "http://" . $this->_solrUsername . ":" . $this->_solrPassword . "@" . $this->_solrUrl . ":" . $this->_solrPort . "/solr/" . $this->_solrCluster . "/select";
        $url .= "?q=" . $idlist;
        //currency
        $currency = strtoupper($this->curr);
        //shipto
        /*if($this->ship_to != "" && $this->ship_to != "00") {
            $shiptolimit = urlencode($this->ship_to . " all");
        } else {
            $shiptolimit = "all";
        }
        $url .= "&fq=shipto:(" . $shiptolimit . ")";*/
        //sorting
        $sort = $this->solrSortList[$this->sortBy];
        if($sort == 'random' || $sort == '') {
            $sort = 'random_' . md5(date('YMDH'));
        }
        $url .= "&sort=" . urlencode($sort . " " . $this->sortDir);
        //page
        $url .= "&start=" . ($this->page -1) * $this->perpage . "&rows=" . $this->perpage;
        //coding
        $url .= "&wt=json";
        //fields
        $url .= "&fl=" . urlencode("id,lowershop,shopnome,nome,prezzos:currency(prezzos," . $currency . "),prezzo:currency(prezzo," . $currency . "),sconto,marcanome,immagine");

        $json = json_decode(file_get_contents($url));

        $return = array();
        $return['products'] = array();
        foreach($json->response->docs as $result) {
            $return['products'][] = array(
                'id' => $result->id,
                'shop' => $result->shopnome,
                'lowershop' => $result->lowershop,
                'nome' => $result->nome,
                'prezzos' => $result->prezzos,
                'prezzo' => $result->prezzo,
                'sconto' => $result->sconto,
                'marca' => $result->marcanome,
                'wishlist' => in_array($result->id, $this->wishlist) ? 1 : 0,
                'mainimg' => $result->immagine
            );
        }

        $return['numFound'] = $json->response->numFound;

        return $return;
    }

    public function loadWishlist($uid) {
        //value
        if($this->curr != "gbp") {
            $cu = strtolower($this->curr);
            $query = "SELECT " . $cu . " FROM rates WHERE key = 'rates'";
            $results = $this->obj->db->query(
                $query
            );
            foreach($results->result_array() as $result) {
                $mult = $result[$cu];
            }
        } else {
            $mult = 1;
        }

        $results = $this->obj->db->query("SELECT p.id, p.reflink, p.nome, p.descrizione, p.quantity, p.sconto, p.prezzo, p.prezzos, b.nome as brandname, s.mysolrname, w.id as wishlist, string_agg(i.immagine, ',') as immagini, ii.immagine as immagine
                 FROM products p
                 JOIN brands b ON b.id = p.marca
                 JOIN shops s ON s.id = p.shop
                 JOIN immagini i ON i.pid = p.id
                 JOIN immagini ii ON (ii.pid = p.id AND ii.main = true)
                 JOIN wishlists w ON (w.productid = p.id AND w.uid = " . $uid . ")
                 GROUP BY p.id, b.nome, s.mysolrname, w.id, ii.immagine");

        return $results;
    }

}