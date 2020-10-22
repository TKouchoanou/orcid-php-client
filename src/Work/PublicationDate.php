<?php


namespace Orcid\Work;


use Exception;

class PublicationDate
{
    /**
     * @var string
     */
 protected $year;
    /**
     * @var string
     */
 protected $month;
    /**
     * @var string
     */
 protected $day;

    /**
     * PublicationDate constructor.
     * @param $year
     * @param string $month
     * @param string $day
     * @throws Exception
     */
    public function __construct(string $year,string $month="",string $day=""){
        $this->setYear($year)->setMonth($month)->setDay($day);
    }

    /**
     * @param string $day
     * @return PublicationDate
     * @throws Exception
     */
    public function setDay(string $day)
    {
        if (!empty($day) && (!is_numeric($day) || strlen((string)$day) > 2 || (int)$day > 31 || (int)$day < 1)) {
              throw new Exception( " \n The day must be a numeric string or a number whose value is between 1 and 31. You have send Day=" . $day);
       }
        $this->day = $day;
        return $this;
    }

    /**
     * @param string $month
     * @return PublicationDate
     * @throws Exception
     */
    public function setMonth(string $month)
    {
        if ((!empty($month) && (!is_numeric($month) || mb_strlen((string)$month) > 2 || (int)$month > 12 || (int)$month < 1))) {
            throw new Exception( " \n The month must be a numeric string or a integer whose value is between 1 and 12. You have send Month=" . $month);
        }
        $this->month = $month;
        return $this;
    }

    /**
     * @param string $year
     * @return PublicationDate
     * @throws Exception
     */
    public function setYear(string $year)
    {
        if(!OAbstractWork::isValidPublicationYear($year)){
            throw new Exception(" \n The year must be a string made up of four numeric characters or be a number of four digits.
             The min value for orcid work year is ".OAbstractWork::PUBLICATION_DATE_MIN_YEAR. " and the max value  is ".OAbstractWork::PUBLICATION_DATE_MAX_YEAR.". You have send Year=" . $year);
        }
        $this->year = $year;
        return $this;
    }

    /**
     * @return string
     */
    public function getYear()
    {
        return $this->year;
    }

    /**
     * @return string
     */
    public function getMonth()
    {
        return $this->month;
    }

    /**
     * @return string
     */
    public function getDay()
    {
        return $this->day;
    }
}