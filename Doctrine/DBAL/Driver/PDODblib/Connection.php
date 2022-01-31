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
 * PDODblib Connection implementation.
 *
 * @since 2.0
 */
class Connection extends \Doctrine\DBAL\Driver\PDOConnection implements \Doctrine\DBAL\Driver\Connection {

    protected $_pdoTransactionsSupport = null;
    protected $_pdoLastInsertIdSupport = null;
        
    public function __construct($dsn, $user = null, $password = null, $options = null)
    {
        parent::__construct($dsn, $user, $password, $options);
        $this->setAttribute(\PDO::ATTR_STATEMENT_CLASS, array(__NAMESPACE__ . '\PDOStatement', array()));
    }
    
    /**
     * @override
     */
    public function quote($value, $type = \PDO::PARAM_STR) {
        $val = parent::quote($value, $type);

        // Fix for a driver version terminating all values with null byte
        $val = rtrim($val, "\0");

        return $val;
    }

    /**
     * @return bool PDO_DBlib transaction support
     */
    private function _pdoTransactionsSupported() {
        if (!is_null($this->_pdoTransactionsSupport)) {
            return $this->_pdoTransactionsSupport;
        }

        $supported = false;
        try {
            $supported = true;
            parent::beginTransaction();
        } catch (\PDOException $e) {
            $supported = false;
        }
        if ($supported) {
            parent::commit();
        }

        return $this->_pdoTransactionsSupport = $supported;
    }

    /**
     * {@inheritdoc}
     */
    public function rollback() {
        if ($this->_pdoTransactionsSupported() === true) {
            parent::rollback();
        } else {
            $this->exec('ROLLBACK TRANSACTION');
        }
    }

    /**
     * {@inheritdoc}
     */
    public function commit() {
        if ($this->_pdoTransactionsSupported() === true) {
            parent::commit();
        } else {
            $this->exec('COMMIT TRANSACTION');
        }
    }

    /**
     * {@inheritdoc}
     */
    public function beginTransaction() {
        if ($this->_pdoTransactionsSupported() === true) {
            parent::beginTransaction();
        } else {
            $this->exec('BEGIN TRANSACTION');
        }
    }

    /**
     * @return bool PDO_DBlib::lastInsertId support
     */
    private function _pdoLastInsertId() {
        if (!is_null($this->_pdoLastInsertIdSupport)) {
            return $this->_pdoLastInsertIdSupport;
        }

        $supported = false;
        try {
            $supported = true;
            parent::lastInsertId();
        } catch (\PDOException $e) {
            $supported = false;
        }

        return $this->_pdoLastInsertIdSupport = $supported;
    }

    /**
     * {@inheritdoc}
     */
    public function lastInsertId($name = null) {
        $id = null;
        if ($this->_pdoLastInsertId() === true) {
            $id = parent::lastInsertId();
        } else {
            $stmt = $this->query('SELECT SCOPE_IDENTITY()');
            $id = $stmt->fetchColumn();
            $stmt->closeCursor();
        }

        return $id;
    }

}
