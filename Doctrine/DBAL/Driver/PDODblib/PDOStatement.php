<?php

namespace Lsw\DoctrinePdoDblib\Doctrine\DBAL\Driver\PDODblib;

class PDOStatement extends \Doctrine\DBAL\Driver\PDOStatement
{
    /**
     * @var string Regex matching freetds non-supported characters.
     */
    const FREETDS_INVALID_CHAR_REGEX = '/[^\x{1}-\x{FFFF}]/u';

    /**
     * {@inheritdoc}
     *
     * Freetds communicates with the server using UCS-2 (since v7.0).
     * Freetds claims to convert from any given client charset to UCS-2 using iconv. Which should strip or replace unsupported chars.
     * However in my experience, characters like 👍 (THUMBS UP SIGN, \u1F44D) still end up in Sqlsrv shouting '102 incorrect syntax'.
     * As does the null character \u0000.
     *
     * Upon binding a value, this function replaces the unsupported characters.
     */
    public function bindValue($param, $value, $type = \PDO::PARAM_STR)
    {
        if ($type == \PDO::PARAM_STR) {
            $value = static::replaceUnsupportedFreetdsChars($value);
        }
        return parent::bindValue($param, $value, $type);
    }

    /**
     * This function replaces characters in a string unsupported by freetds with the REPLACEMENT CHARACTER.
     * @param string $val
     * @return string
     */
    public static function replaceUnsupportedFreetdsChars($val)
    {
        return is_string($val) ? \preg_replace(static::FREETDS_INVALID_CHAR_REGEX, "�", $val) : $val;
    }
}
