<?php

/**
 * Converts a JSON RRULE object to an iCalendar RRULE string.
 *
 * @param array $jsonRRule JSON-decoded array containing RRULE components.
 * @return string The iCalendar RRULE string.
 * @throws InvalidArgumentException If required fields are missing or invalid.
 */
function convertRRuleJsonToICal(array $rrule): string
{
    // // Ensure 'rrule' key exists
    // if (!isset($jsonRRule['rrule']) || !is_array($jsonRRule['rrule'])) {
    //     throw new InvalidArgumentException('Invalid JSON structure: "rrule" key is missing or not an array.');
    // }

    // $rrule = $jsonRRule['rrule'];
    
    // Validate required FREQ field
    if (!isset($rrule['FREQ']) || !in_array($rrule['FREQ'], ['SECONDLY', 'MINUTELY', 'HOURLY', 'DAILY', 'WEEKLY', 'MONTHLY', 'YEARLY'])) {
        throw new InvalidArgumentException('FREQ is required and must be one of: SECONDLY, MINUTELY, HOURLY, DAILY, WEEKLY, MONTHLY, YEARLY');
    }

    // Initialize RRULE parts
    $parts = [];
    $parts[] = 'FREQ=' . $rrule['FREQ'];

    // Handle optional fields
    if (isset($rrule['INTERVAL']) && is_int($rrule['INTERVAL']) && $rrule['INTERVAL'] >= 1) {
        $parts[] = 'INTERVAL=' . $rrule['INTERVAL'];
    }

    if (isset($rrule['UNTIL']) && is_string($rrule['UNTIL'])) {
        // Validate UNTIL format (YYYYMMDDTHHMMSSZ or YYYYMMDD)
        if (!preg_match('/^\d{8}(T\d{6}Z)?$/', $rrule['UNTIL'])) {
            throw new InvalidArgumentException('UNTIL must be in YYYYMMDDTHHMMSSZ or YYYYMMDD format');
        }
        if (isset($rrule['COUNT'])) {
            throw new InvalidArgumentException('UNTIL and COUNT are mutually exclusive');
        }
        $parts[] = 'UNTIL=' . $rrule['UNTIL'];
    }

    if (isset($rrule['COUNT']) && is_int($rrule['COUNT']) && $rrule['COUNT'] >= 1) {
        $parts[] = 'COUNT=' . $rrule['COUNT'];
    }

    if (isset($rrule['BYSECOND']) && is_array($rrule['BYSECOND'])) {
        $seconds = array_filter($rrule['BYSECOND'], function ($val) {
            return is_int($val) && $val >= 0 && $val <= 60;
        });
        if (!empty($seconds)) {
            $parts[] = 'BYSECOND=' . implode(',', $seconds);
        }
    }

    if (isset($rrule['BYMINUTE']) && is_array($rrule['BYMINUTE'])) {
        $minutes = array_filter($rrule['BYMINUTE'], function ($val) {
            return is_int($val) && $val >= 0 && $val <= 59;
        });
        if (!empty($minutes)) {
            $parts[] = 'BYMINUTE=' . implode(',', $minutes);
        }
    }

    if (isset($rrule['BYHOUR']) && is_array($rrule['BYHOUR'])) {
        $hours = array_filter($rrule['BYHOUR'], function ($val) {
            return is_int($val) && $val >= 0 && $val <= 23;
        });
        if (!empty($hours)) {
            $parts[] = 'BYHOUR=' . implode(',', $hours);
        }
    }

    if (isset($rrule['BYDAY']) && is_array($rrule['BYDAY'])) {
        $days = array_filter($rrule['BYDAY'], function ($val) {
            return is_string($val) && preg_match('/^([+-]?\d{1,2})?(SU|MO|TU|WE|TH|FR|SA)$/', $val);
        });
        if (!empty($days)) {
            $parts[] = 'BYDAY=' . implode(',', $days);
        }
    }

    if (isset($rrule['BYMONTHDAY']) && is_array($rrule['BYMONTHDAY'])) {
        $monthDays = array_filter($rrule['BYMONTHDAY'], function ($val) {
            return is_int($val) && (($val >= 1 && $val <= 31) || ($val >= -31 && $val <= -1));
        });
        if (!empty($monthDays)) {
            $parts[] = 'BYMONTHDAY=' . implode(',', $monthDays);
        }
    }

    if (isset($rrule['BYYEARDAY']) && is_array($rrule['BYYEARDAY'])) {
        if (in_array($rrule['FREQ'], ['WEEKLY', 'MONTHLY'])) {
            throw new InvalidArgumentException('BYYEARDAY is not valid for WEEKLY or MONTHLY frequencies');
        }
        $yearDays = array_filter($rrule['BYYEARDAY'], function ($val) {
            return is_int($val) && (($val >= 1 && $val <= 366) || ($val >= -366 && $val <= -1));
        });
        if (!empty($yearDays)) {
            $parts[] = 'BYYEARDAY=' . implode(',', $yearDays);
        }
    }

    if (isset($rrule['BYWEEKNO']) && is_array($rrule['BYWEEKNO'])) {
        if ($rrule['FREQ'] !== 'YEARLY') {
            throw new InvalidArgumentException('BYWEEKNO is only valid for YEARLY frequency');
        }
        $weekNos = array_filter($rrule['BYWEEKNO'], function ($val) {
            return is_int($val) && (($val >= 1 && $val <= 53) || ($val >= -53 && $val <= -1));
        });
        if (!empty($weekNos)) {
            $parts[] = 'BYWEEKNO=' . implode(',', $weekNos);
        }
    }

    if (isset($rrule['BYMONTH']) && is_array($rrule['BYMONTH'])) {
        $months = array_filter($rrule['BYMONTH'], function ($val) {
            return is_int($val) && $val >= 1 && $val <= 12;
        });
        if (!empty($months)) {
            $parts[] = 'BYMONTH=' . implode(',', $months);
        }
    }

    if (isset($rrule['BYSETPOS']) && is_array($rrule['BYSETPOS'])) {
        $setPos = array_filter($rrule['BYSETPOS'], function ($val) {
            return is_int($val) && (($val >= 1 && $val <= 366) || ($val >= -366 && $val <= -1));
        });
        if (!empty($setPos)) {
            $parts[] = 'BYSETPOS=' . implode(',', $setPos);
        }
    }

    if (isset($rrule['WKST']) && in_array($rrule['WKST'], ['SU', 'MO', 'TU', 'WE', 'TH', 'FR', 'SA'])) {
        $parts[] = 'WKST=' . $rrule['WKST'];
    }

    // Combine parts into RRULE string
    return 'RRULE:' . implode(';', $parts);
}


?>