<?php

class CreateDatePeriod {

    private $_period;
    private $_startMonth;
    private $_months;

    /*
     * @parameters String $startMonth, int $months
     * saves private variables
     */
    public function __construct($startMonth, int $months = 12) {

        if($this->_validateDate($startMonth)) {
            $this->_startMonth = $startMonth;
        } else {
            throw new CustomException("Start Month is not correct", "Please check your start Month date (" . $startMonth . ")");
        }

        if($months > 0) {
            $this->_months = $months;
        } else {
            throw new CustomException("Period of Months Incorrect", "Please check your number of months to take into account");
        }

        $this->_calculatePeriod();
    }

    /*
     * @parameters void
     * returns DateTime object
     */
    public function load() {
        return $this->_period;
    }

    /*
     * DateTime period calc
     * @parameters none
     * Returns $this;
     */
   private function _calculatePeriod()
   {
        $start = new DateTime($this->_startMonth);
        $start->modify('first day of this month');
        $end = new DateTime($this->_startMonth);
        $end->add(new DateInterval('P' . $this->_months . 'M'));
        $end->modify('last day of this month');
        $interval = DateInterval::createFromDateString('1 month');

        $this->_period   = new DatePeriod($start, $interval, $end);

        return $this;
   }

    /*
     * Date Validation
     * @parameters String $date
     * Returns bool
     */
   private function _validateDate($date)
   {
       $d = DateTime::createFromFormat('d-m-Y', $date);
       return $d && $d->format('d-m-Y') == $date;
   }


}