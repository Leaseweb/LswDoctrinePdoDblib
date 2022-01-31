<?php
/*
 *  $Id$
 *
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS
 * "AS IS" AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT
 * LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR
 * A PARTICULAR PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT
 * OWNER OR CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL,
 * SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT
 * LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES; LOSS OF USE,
 * DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND ON ANY
 * THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT
 * (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE
 * OF THIS SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
 *
 * This software consists of voluntary contributions made by many individuals
 * and is licensed under the LGPL. For more information, see
 * <http://www.doctrine-project.org>.
 */

namespace Lsw\DoctrinePdoDblib\Doctrine\DBAL\Driver\PDODblib;

/**
 * The PDO-based Dblib driver.
 *
 * @since 2.0
 */
class Driver implements \Doctrine\DBAL\Driver {
    public function connect(array $params, $username = null, $password = null, array $driverOptions = array()) {
        return new Connection(
            $this->_constructPdoDsn($params),
            $username,
            $password,
            $driverOptions
        );
    }

    /**
     * Constructs the Dblib PDO DSN.
     *
     * @return string  The DSN.
     */
    private function _constructPdoDsn(array $params) {
        $dsn = 'dblib:host=';

        if (isset($params['host'])) {
            $dsn .= $params['host'];
        }

        if (isset($params['port']) && !empty($params['port'])) {
            $portSeparator = (PATH_SEPARATOR === ';') ? ',' : ':';
            $dsn .= $portSeparator . $params['port'];
        }

        if (isset($params['dbname'])) {
            $dsn .= ';dbname=' . $params['dbname'];
        }

        if (isset($params['charset'])) {
            $dsn .= ';charset=' . $params['charset'];
        }

        if (isset($params['version'])) {
            $dsn .= ';version=' . $params['version'];
        }

        return $dsn;
    }

    public function getDatabasePlatform() {
        if (class_exists('\\Lsw\\DoctrinePdoDblib\\Doctrine\\Platforms\\SQLServer2008Platform')) {
            return new \Lsw\DoctrinePdoDblib\Doctrine\Platforms\SQLServer2008Platform();
        }
        
        if (class_exists('\\Doctrine\\DBAL\\Platforms\\SQLServer2008Platform')) {
            return new \Doctrine\DBAL\Platforms\SQLServer2008Platform();
        }

        if (class_exists('\\Doctrine\\DBAL\\Platforms\\SQLServer2005Platform')) {
            return new \Doctrine\DBAL\Platforms\SQLServer2005Platform();
        }
        
        if (class_exists('\\Doctrine\\DBAL\\Platforms\\MsSqlPlatform')) {
            return new \Doctrine\DBAL\Platforms\MsSqlPlatform();
        }
    }

    public function getSchemaManager(\Doctrine\DBAL\Connection $conn) {
        if (class_exists('\\Doctrine\\DBAL\\Schema\\SQLServerSchemaManager')) {
            return new \Doctrine\DBAL\Schema\SQLServerSchemaManager($conn);
        }

        if (class_exists('\\Doctrine\\DBAL\\Schema\\MsSqlSchemaManager')) {
            return new \PDODblibBundle\Doctrine\DBAL\Schema\PDODblibSchemaManager($conn);
        }


    }

    public function getName() {
        return 'pdo_dblib';
    }

    public function getDatabase(\Doctrine\DBAL\Connection $conn) {
        $params = $conn->getParams();
        return $params['dbname'];
    }
}
