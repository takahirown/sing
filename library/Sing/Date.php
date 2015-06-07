<?php

/**
 * 日付ユーティリティクラス
 *
 * @category   Sing
 * @author     t.watanabe
 * @since      2014/07/26
 */
class Sing_Date
{
    /**
     * 現在時刻を取得します。
     *
     * @return string
     */
    public static function getNow()
    {
        return date('Y-m-d H:i:s');
    }

    /**
     * 現在時刻(マイクロ秒込み）を取得します。
     *
     * @return string
     */
    public static function getNowMicro()
    {
        list($micro, $Unixtime) = explode(" ", microtime());
        $sec = $micro + date("s", $Unixtime);
        return date("Y-m-d H:i:s:", $Unixtime).$sec;
    }

    /**
     * DB時刻(2014/02/11 01:45:32)を渡し、3分前・1時間前のような表記に変換します。
     *
     * @param string $time
     * @return string
     */
    public static function convertFuzzyTime($time)
    {
        $unix   = strtotime($time);
        $now    = time();
        $diff_sec   = $now - $unix;

        if ($diff_sec < 60) {
            $time   = $diff_sec;
            $unit   = "秒前";
        } elseif ($diff_sec < 3600) {
            $time   = $diff_sec/60;
            $unit   = "分前";
        } elseif ($diff_sec < 86400) {
            $time   = $diff_sec/3600;
            $unit   = "時間前";
        } elseif ($diff_sec < 2764800) {
            $time   = $diff_sec/86400;
            $unit   = "日前";
        } else {
            if (date("Y") != date("Y", $unix)) {
                $time   = date("Y年n月j日", $unix);
            } else {
                $time   = date("n月j日", $unix);
            }
            return $time;
        }
        return (int)$time . $unit;
    }

    /**
     * 今日が月の何週目か取得します。
     *
     * @return int
     */
    public static function getWeekNumber()
    {
        $timestamp = time();
        $maxday    = date("t",$timestamp);
        $thismonth = getdate($timestamp);
        $timeStamp = mktime(0,0,0,$thismonth['mon'],1,$thismonth['year']);
        $startday  = date('w',$timeStamp);
        $day = $thismonth['mday'];
        $weeks = 0;
        $week_num = 0;
        for ($i=0; $i<($maxday+$startday); $i++) {
            if(($i % 7) == 0){
                $weeks++;
            }
            if($day == ($i - $startday + 1)){
                $week_num = $weeks;
            }
        }
        return $week_num;
    }

    /**
     * 2つの日付の差を取得します。
     *
     * @param string $date1
     * @param string $date2
     * @return number
     */
    public static function getDayDiff($date1, $date2)
    {
        // 日付をUNIXタイムスタンプに変換
        $timestamp1 = strtotime($date1);
        $timestamp2 = strtotime($date2);

        // 何秒離れているかを計算
        $seconddiff = $timestamp2 - $timestamp1;

        // 日数に変換
        return $seconddiff / (60 * 60 * 24);
    }

}
