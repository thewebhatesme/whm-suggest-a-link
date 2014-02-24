<?php

class DateCalculator
{

  private $configFile;

  private $weekDays = array(
      'Monday',
      'Tuesday',
      'Wednesday',
      'Thursday',
      'Friday',
      'Saturday',
      'Sunday'
  );

  private $dateInterval = array();

  private $lastLinkDate = array();

  private $moderatorEmail;

  private $salt;

  public function __construct ($configFile)
  {
    $this->configFile = $configFile;
    $this->loadConfig();
  }

  private function loadConfig ()
  {
    $config = file($this->configFile);

    $blogDays = explode(',', $config[1]);
    $blogTimes = explode(',', $config[4]);
    $this->dateInterval = array(
        $blogDays,
        $blogTimes
    );

    $lastLinkDate = explode(',', $config[13]);
    $this->lastLinkDate = array(
        $lastLinkDate[0],
        (int) $lastLinkDate[1]
    );
    $this->salt = $config[7];
    $this->moderatorEmail = $config[10];
  }

  public function getModeratorEmail ()
  {
    return trim($this->moderatorEmail);
  }

  private function getLastLinkDate ()
  {
    return $this->lastLinkDate;
  }

  private function getDateInterval ()
  {
    return $this->dateInterval;
  }

  public function getNextDate ()
  {
    $lastLinkDateTimeArray = $this->getLastLinkDate();
    $latestLinkDate = $lastLinkDateTimeArray[0];
    $latestLinkTime = $lastLinkDateTimeArray[1];

    $dayBeforeLatestLinkDate = date("Y-m-d", strtotime($latestLinkDate . " -1 day"));

    $dateInterval = $this->getDateInterval();

    $blogDays = $dateInterval[0];
    $blogTimes = $dateInterval[1];

    $nextBlogTime = $blogTimes[0];
    $daysToAdd = 1;

    foreach ($blogTimes as $blogTime) {
      if ((int) $blogTime > (int) $latestLinkTime) {
        $nextBlogTime = (int) $blogTime;
        $daysToAdd = 0;
        break;
      }
    }

    $dayBeforeLatestLinkDate = date("Y-m-d", strtotime($dayBeforeLatestLinkDate . " " . $daysToAdd . " day"));

    foreach ($blogDays as $blogDay) {
      $dateString = $dayBeforeLatestLinkDate . " next " . $this->weekDays[(int) $blogDay];
      $nextBlogDate[] = date("Y-m-d", strtotime($dateString));
    }

    sort($nextBlogDate);

    $nextBlogDateTime = $nextBlogDate[0] . " " . $nextBlogTime . ":00:00";
    return date('Y-m-d H:i:s', strtotime($nextBlogDateTime));
  }

  public function getSalt ()
  {
    return $this->salt;
  }

  public function setLastLinkDate ($date)
  {
    $time = date('H', strtotime($date));
    $day = date('Y-m-d', strtotime($date));

    $data = "# Weekdays there should be links\n";
    $data .= implode(',', $this->dateInterval[0]) . "\n";

    $data .= "# Hours there should be links\n";
    $data .= implode(',', $this->dateInterval[1]) . "\n";

    $data .= "# Salt\n";
    $data .= $this->salt . "\n";

    $data .= "# Moderator E-Mail\n";
    $data .= $this->moderatorEmail . "\n";

    $data .= "# Last link date\n";
    $data .= $day . ', ' . $time;

    file_put_contents($this->configFile, $data);
  }
}