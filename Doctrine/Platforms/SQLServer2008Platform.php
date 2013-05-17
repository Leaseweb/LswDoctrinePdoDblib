<?php

namespace Lsw\DoctrinePdoDblib\Doctrine\Platforms;
use Doctrine\DBAL\Platforms\SQLServer2008Platform as SQLServer;

class SQLServer2008Platform extends SQLServer
{
  
    /**
     * {@inheritDoc}
     */
    public function getDateTimeFormatString()
    {
        return 'Y-m-d H:i:s';
    }
    
    /**
     * {@inheritDoc}
     */
    public function getDateTimeTzFormatString()
    {
        return $this->getDateTimeFormatString();
    }
}