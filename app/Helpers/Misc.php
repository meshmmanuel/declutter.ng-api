<?php

use Cocur\Slugify\Slugify as CocurSlugify;
// use Twilio\Rest\Client as TWClient;

/**
 * function extension of cocur/slugify
 * @param string $string
 * @param string $delimiter
 * @return string
 */
function slugify($string, $delimiter = '-')
{
    $slugify = new CocurSlugify;
    return $slugify->slugify($string, $delimiter);
}

function generateCode()
{
    return rand(100000, 999999);
}

function formatPhoneNumber($phoneNumber)
{
    return substr(addCountryCode(str_replace(' ', '', $phoneNumber)), 1);
}

function addCountryCode($phoneNumber)
{
    if (!is_string($phoneNumber)) {
        return $phoneNumber;
    }

    // remove white spaces
    $phoneNumber = trim($phoneNumber);

    // if starts with a plus sign return phone number
    if (strpos($phoneNumber, '+') === 0) {
        return $phoneNumber;
    }

    // if numbr starts with a zero add 234
    if (strpos($phoneNumber, '0') === 0) {
        $phoneNumber = substr($phoneNumber, 1, strlen($phoneNumber));
        return '+234' . $phoneNumber;
    }

    // assumes zip code was entered without the plus sign
    if (strpos($phoneNumber, '0') !== 0 && strpos($phoneNumber, '+') !== 0) {
        return '+' . $phoneNumber;
    }

    // return the value that was given
    return $phoneNumber;
}

function sanitizePhoneNumber($phoneNumber)
{
    return str_replace(' ', '', addCountryCode($phoneNumber));
}

function sendSms($recipient, $message)
{
    // get twilio config
    $config = config("services.twilio");
    // stop and return false if required data is not found
    if (
        is_null($config["sid"]) ||
        is_null($config["token"]) ||
        is_null($config["number"])
    ) return false;

    // create client
    // $client = new TWClient($config["sid"], $config["token"]);
    // create and send message, then return status
    return $client->messages
        ->create(
            addCountryCode(str_replace(' ', '', $recipient)), // to
            [
                "from" => $config["number"],
                "body" => $message
            ]
        );
}

function getNextCharge(
    string $frequency,
    string $autosaveTime,
    int $frequencyDay = 0
) {
    // determine
    switch ($frequency) {
        case 'daily':
            return nextDailyCharge($autosaveTime);
            break;
        case 'weekly':
            return nextWeeklyCharge($autosaveTime, $frequencyDay);
            break;
        case 'monthly':
            return nextMonthlyCharge($autosaveTime, $frequencyDay);
            break;
        default:
            return null;
            break;
    }
}

function nextDailyCharge(string $time)
{
    // return null if time was not passed
    if (!preg_match('/^\d\d\:\d\d(\:\d\d)?$/', $time)) return null;

    // get next time date
    $nextTime = date('Y-m-d ' . $time);

    // check if next time is ahead of current time
    if (strtotime($nextTime) > time()) return $nextTime;

    // make next charge next day the same time
    return date('Y-m-d ' . $time, strtotime(now()->addDay(1)));
}

function nextWeeklyCharge(string $time, int $day)
{
    // return null if time was not passed
    if (!preg_match('/^\d\d\:\d\d(\:\d\d)?$/', $time)) return null;

    // return null if the day is not an int btween 1 to 7
    if ($day < 1 || $day > 7) return null;

    // days list
    $days = [
        'monday', 'tuesday',
        'wednesday', 'thursday',
        'friday', 'saturday',
        'sunday'
    ];

    // get next time based on day
    $nextTime = date('Y-m-d ' . $time, strtotime($days[$day - 1] . ' this week'));

    // check if next time is ahead of current time
    if (strtotime($nextTime) > time()) return $nextTime;

    // make next charge next week the same day
    return date('Y-m-d ' . $time, strtotime($days[$day - 1] . ' next week'));
}

function nextMonthlyCharge(string $time, int $day)
{
    // return null if time was not passed
    if (!preg_match(
        '/^\d\d\:\d\d(\:\d\d)?$/',
        $time
    )) return null;

    // return null if the day is not an int btween 1 to 7
    if ($day < 1 || $day > 28) return null;

    // pad day with a zero if a single digit
    if (preg_match('/^\d$/', $day)) $day = '0' . $day;

    // get next time
    $nextTime = date('Y-m-' . $day . ' ' . $time);

    // return next time if greater than current time
    if (strtotime($nextTime) > time()) return $nextTime;

    // make next charge same day and time next month
    return date('Y-m-' . $day . ' ' . $time, strtotime(now()->addMonth(1)));
}

function encrypto(string $data, $key = null, $iv = null)
{
    $key = !is_null($key) ? $key : env('QR_ENC_KEY');
    $iv = !is_null($iv) ? $iv : substr(env('QR_ENC_KEY'), -16);
    return trim(openssl_encrypt($data, 'AES-256-CBC', $key, null, $iv));
}

function decrypto(string $data, $key = null, $iv = null)
{
    $key = !is_null($key) ? $key : env('QR_ENC_KEY');
    $iv = !is_null($iv) ? $iv : substr(env('QR_ENC_KEY'), -16);
    return trim(openssl_decrypt($data, 'AES-256-CBC', $key, null, $iv));
}

function dbDateTimeFormatter($date)
{
    if (!$date) {
        $date = (string) time();
    }
    return date('Y-m-d H:i:s', strtotime($date));
}

function padNumber(int $number, $amount = 2, $prepend = false)
{
    $strNumber = (string) $number;
    $remainder = $amount - strlen($strNumber);
    $zeros = '';
    if ($remainder >= 1) {
        for ($i = 0; $i < $remainder; $i++) {
            if ($prepend) {
                $zeros = '0' . $zeros;
                continue;
            }
            $zeros .= '0';
        }
    }
    return $zeros . $strNumber;
}

/*::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::*/
/*::                                                                         :*/
/*::  This routine calculates the distance between two points (given the     :*/
/*::  latitude/longitude of those points). It is being used to calculate     :*/
/*::  the distance between two locations using GeoDataSource(TM) Products    :*/
/*::                                                                         :*/
/*::  Definitions:                                                           :*/
/*::    South latitudes are negative, east longitudes are positive           :*/
/*::                                                                         :*/
/*::  Passed to function:                                                    :*/
/*::    lat1, lon1 = Latitude and Longitude of point 1 (in decimal degrees)  :*/
/*::    lat2, lon2 = Latitude and Longitude of point 2 (in decimal degrees)  :*/
/*::                                                                         :*/
/*::  Worldwide cities and other features databases with latitude longitude  :*/
/*::  are available at https://www.geodatasource.com                          :*/
/*::                                                                         :*/
/*::  For enquiries, please contact sales@geodatasource.com                  :*/
/*::                                                                         :*/
/*::  Official Web site: https://www.geodatasource.com                        :*/
/*::                                                                         :*/
/*::         GeoDataSource.com (C) All Rights Reserved 2018                  :*/
/*::                                                                         :*/
/*::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::*/
function getDistance($lat1, $lon1, $lat2, $lon2)
{
    if (($lat1 == $lat2) && ($lon1 == $lon2)) {
        return 0;
    } else {
        $theta = $lon1 - $lon2;
        $dist = sin(deg2rad($lat1)) * sin(deg2rad($lat2)) +  cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * cos(deg2rad($theta));
        $dist = acos($dist);
        $dist = rad2deg($dist);
        $miles = $dist * 60 * 1.1515;
        // return in kilo meters
        return $miles * 1.609344;
    }
}

function renameFile($extension)
{
    return time() . '' . rand(10000, 90000) . '.' . $extension;
}
