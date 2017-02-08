<?php 

class CreateData {

    private $_data = array();
    private $_period;

    public function __construct() {

    }

    /*
     * @parameters String $method
     * returns $this
     */
    public function createData($method) {
        $mtl = "_" . $method;
        $this->$mtl();

        return $this;
    }

    /*
     * @parameters DateInterval $period
     * returns $this
     */
    public function setPeriod($period) {
        $this->_period = $period;
        return $this;
    }

    /*
     * @parameters void
     * returns array
     */
    public function retrieveData() {
        return $this->_data;
    }

    /*
     * @params void
     * returns void
     */
    private function _limesharp() {
        foreach ($this->_period as $dt) {
            //define salary pay date
            $dt->modify('last day of this month');
            if($dt->format('N') == 7) {
                $dt->sub(new DateInterval('P2D'));
            } elseif($dt->format('N') == 6) {
                $dt->sub(new DateInterval('P1D'));
            }
            $month = $dt->format("F Y");
            $paymentDate = $dt->format("d/m/Y");

            //define bonus payment date
            //15
            $dt->modify("first day of this month")->add(new DateInterval("P14D"));
            if($dt->format('N') == 7) {
                $dt->add(new DateInterval("P3D"));
            } elseif($dt->format('N') == 6) {
                $dt->add(new DateInterval("P4D"));
            }
            $bonusDate = $dt->format("d/m/Y");

            $this->_data[] = array($month, $paymentDate, $bonusDate);
        }
    }
}