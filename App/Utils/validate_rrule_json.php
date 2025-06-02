<?php

/**
 * Validates a JSON RRULE object for iCalendar compliance.
 *
 * @param array $jsonRRule JSON-decoded array with direct RRULE component values.
 * @return bool True if the JSON is valid.
 * @throws InvalidArgumentException If the JSON is invalid or violates iCalendar rules.
 */
function validateRRuleJson(array $rrule): bool
{
    // // Check if 'rrule' key exists and is an array
    // if (!isset($jsonRRule['rrule']) || !is_array($jsonRRule['rrule'])) {
    //     throw new InvalidArgumentException('Invalid JSON structure: "rrule" key is missing or not an array.');
    // }

    // $rrule = $jsonRRule['rrule'];

    // Validate required FREQ field
    if (!isset($rrule['FREQ']) || !is_string($rrule['FREQ']) || !in_array($rrule['FREQ'], ['SECONDLY', 'MINUTELY', 'HOURLY', 'DAILY', 'WEEKLY', 'MONTHLY', 'YEARLY'])) {
        throw new InvalidArgumentException('FREQ is required and must be one of: SECONDLY, MINUTELY, HOURLY, DAILY, WEEKLY, MONTHLY, YEARLY');
    }

    // Validate INTERVAL
    if (isset($rrule['INTERVAL'])) {
        if (!is_int($rrule['INTERVAL']) || $rrule['INTERVAL'] < 1) {
            throw new InvalidArgumentException('INTERVAL must be a positive integer');
        }
    }

    // Validate UNTIL
    if (isset($rrule['UNTIL'])) {
        if (!is_string($rrule['UNTIL']) || !preg_match('/^\d{8}(T\d{6}Z)?$/', $rrule['UNTIL'])) {
            throw new InvalidArgumentException('UNTIL must be in YYYYMMDDTHHMMSSZ or YYYYMMDD format');
        }
        if (isset($rrule['COUNT'])) {
            throw new InvalidArgumentException('UNTIL and COUNT are mutually exclusive');
        }
    }

    // Validate COUNT
    if (isset($rrule['COUNT'])) {
        if (!is_int($rrule['COUNT']) || $rrule['COUNT'] < 1) {
            throw new InvalidArgumentException('COUNT must be a positive integer');
        }
    }

    // Validate BYSECOND
    if (isset($rrule['BYSECOND'])) {
        if (!is_array($rrule['BYSECOND'])) {
            throw new InvalidArgumentException('BYSECOND must be an array of integers');
        }
        foreach ($rrule['BYSECOND'] as $val) {
            if (!is_int($val) || $val < 0 || $val > 60) {
                throw new InvalidArgumentException('BYSECOND values must be integers between 0 and 60');
            }
        }
    }

    // Validate BYMINUTE
    if (isset($rrule['BYMINUTE'])) {
        if (!is_array($rrule['BYMINUTE'])) {
            throw new InvalidArgumentException('BYMINUTE must be an array of integers');
        }
        foreach ($rrule['BYMINUTE'] as $val) {
            if (!is_int($val) || $val < 0 || $val > 59) {
                throw new InvalidArgumentException('BYMINUTE values must be integers between 0 and 59');
            }
        }
    }

    // Validate BYHOUR
    if (isset($rrule['BYHOUR'])) {
        if (!is_array($rrule['BYHOUR'])) {
            throw new InvalidArgumentException('BYHOUR must be an array of integers');
        }
        foreach ($rrule['BYHOUR'] as $val) {
            if (!is_int($val) || $val < 0 || $val > 23) {
                throw new InvalidArgumentException('BYHOUR values must be integers between 0 and 23');
            }
        }
    }

    // Validate BYDAY
    if (isset($rrule['BYDAY'])) {
        if (!is_array($rrule['BYDAY'])) {
            throw new InvalidArgumentException('BYDAY must be an array of strings');
        }
        foreach ($rrule['BYDAY'] as $val) {
            if (!is_string($val) || !preg_match('/^([+-]?\d{1,2})?(SU|MO|TU|WE|TH|FR|SA)$/', $val)) {
                throw new InvalidArgumentException('BYDAY values must be in format [+-][1-53][SU,MO,TU,WE,TH,FR,SA], e.g., MO, +1MO, -2TU');
            }
        }
    }

    // Validate BYMONTHDAY
    if (isset($rrule['BYMONTHDAY'])) {
        if (!is_array($rrule['BYMONTHDAY'])) {
            throw new InvalidArgumentException('BYMONTHDAY must be an array of integers');
        }
        foreach ($rrule['BYMONTHDAY'] as $val) {
            if (!is_int($val) || ($val < -31 || $val > 31) || $val === 0) {
                throw new InvalidArgumentException('BYMONTHDAY values must be integers between -31 and -1 or 1 and 31');
            }
        }
    }

    // Validate BYYEARDAY
    if (isset($rrule['BYYEARDAY'])) {
        if (in_array($rrule['FREQ'], ['WEEKLY', 'MONTHLY'])) {
            throw new InvalidArgumentException('BYYEARDAY is not valid for WEEKLY or MONTHLY frequencies');
        }
        if (!is_array($rrule['BYYEARDAY'])) {
            throw new InvalidArgumentException('BYYEARDAY must be an array of integers');
        }
        foreach ($rrule['BYYEARDAY'] as $val) {
            if (!is_int($val) || ($val < -366 || $val > 366) || $val === 0) {
                throw new InvalidArgumentException('BYYEARDAY values must be integers between -366 and -1 or 1 and 366');
            }
        }
    }

    // Validate BYWEEKNO
    if (isset($rrule['BYWEEKNO'])) {
        if ($rrule['FREQ'] !== 'YEARLY') {
            throw new InvalidArgumentException('BYWEEKNO is only valid for YEARLY frequency');
        }
        if (!is_array($rrule['BYWEEKNO'])) {
            throw new InvalidArgumentException('BYWEEKNO must be an array of integers');
        }
        foreach ($rrule['BYWEEKNO'] as $val) {
            if (!is_int($val) || ($val < -53 || $val > 53) || $val === 0) {
                throw new InvalidArgumentException('BYWEEKNO values must be integers between -53 and -1 or 1 and 53');
            }
        }
    }

    // Validate BYMONTH
    if (isset($rrule['BYMONTH'])) {
        if (!is_array($rrule['BYMONTH'])) {
            throw new InvalidArgumentException('BYMONTH must be an array of integers');
        }
        foreach ($rrule['BYMONTH'] as $val) {
            if (!is_int($val) || $val < 1 || $val > 12) {
                throw new InvalidArgumentException('BYMONTH values must be integers between 1 and 12');
            }
        }
    }

    // Validate BYSETPOS
    if (isset($rrule['BYSETPOS'])) {
        if (!is_array($rrule['BYSETPOS'])) {
            throw new InvalidArgumentException('BYSETPOS must be an array of integers');
        }
        foreach ($rrule['BYSETPOS'] as $val) {
            if (!is_int($val) || ($val < -366 || $val > 366) || $val === 0) {
                throw new InvalidArgumentException('BYSETPOS values must be integers between -366 and -1 or 1 and 366');
            }
        }
    }

    // Validate WKST
    if (isset($rrule['WKST'])) {
        if (!is_string($rrule['WKST']) || !in_array($rrule['WKST'], ['SU', 'MO', 'TU', 'WE', 'TH', 'FR', 'SA'])) {
            throw new InvalidArgumentException('WKST must be one of: SU, MO, TU, WE, TH, FR, SA');
        }
    }

    // Ensure no unexpected keys
    $validKeys = [
        'FREQ', 'INTERVAL', 'UNTIL', 'COUNT', 'BYSECOND', 'BYMINUTE', 'BYHOUR',
        'BYDAY', 'BYMONTHDAY', 'BYYEARDAY', 'BYWEEKNO', 'BYMONTH', 'BYSETPOS', 'WKST'
    ];
    foreach (array_keys($rrule) as $key) {
        if (!in_array($key, $validKeys)) {
            throw new InvalidArgumentException("Invalid RRULE key: $key");
        }
    }

    return true;
}