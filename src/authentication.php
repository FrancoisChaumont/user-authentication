<?php

    /**
     * Library to attempt user authentication verifying its credentials stored in a database
     */
    class Authentication {
/* member variables */
        private $db; // db object
        private $authenticated;
        private $userTable; // table name for users
        private $userUsernameColumn; // column name for users username
        private $userPasswordColumn; // column name for users password
        private $userIdColumn; // column name for users id
        private $userID = ''; // ID of authenticated user
        
/* member functions */
        public function deauthenticate() { $this->authenticated = false; $this->userID = ''; }
        public function isConnected() { return $this->db->isConnected(); }
        public function isAuthenticated() { return $this->authenticated; }
        public function getUserID() { return $this->userID; }

/* constructor */
        /**
         * Only used by static instantiators
         */
        private function __construct() { 
            $this->authenticated = false;
        }

        /**
         * Instantiator with param db only
         *
         * @param Db $parDb
         * @param string $parUserTable SQL user table name
         * @param string $parUserUsername SQL user table username column name
         * @param string $parUserPassword SQL user table password column name
         * @param string $parUserId SQL user table ID column name
         * @return Authentication or null
         */
        public static function newNoLogin(Db $parDb, string $parUserTable, string $parUserUsername, string $parUserPassword, string $parUserId) {
            $instance = new self(); // instanciate a new Auth object via constructor
            $instance->db = $parDb; // allocate a Db object to the member variable $db
            
            // setting table and columns name to use for authentication purpose
            $instance->userTable = $parUserTable;
            $instance->userUsernameColumn = $parUserUsername;
            $instance->userPasswordColumn = $parUserPassword;
            $instance->userIdColumn = $parUserId;

            if ($instance->verifyUserTableInfo()) { return $instance; }
            else { return null; }
        }

        /**
         * Instantiator with param db, login and password
         *
         * @param Db $parDb
         * @param string $parUserTable SQL user table name
         * @param string $parUserUsername SQL user table username column name
         * @param string $parUserPassword SQL user table password column name
         * @param string $parUserId SQL user table ID column name
         * @param string $parLogin user's login
         * @param string $parPwd user's password
         * @return Authentication or null
         */
        public static function newWithLogin(Db $parDb, string $parUserTable, string $parUserUsername, string $parUserPassword, string $parUserId, string $parLogin, string $parPwd) {
            $instance = new self(); // instanciate a new Auth object via constructor
            $instance->db = $parDb; // allocate a Db object to the member variable $db

            // setting table and columns name to use for authentication purpose
            $instance->userTable = $parUserTable;
            $instance->userUsernameColumn = $parUserUsername;
            $instance->userPasswordColumn = $parUserPassword;
            $instance->userIdColumn = $parUserId;

            if (!($instance->verifyUserTableInfo())) { return null; }

            // attempt to authenticate the user using login and password
            if ($instance->isConnected()) { 
                $instance->authenticate($parLogin, $parPwd);
                if ($instance->isAuthenticated()) { return $instance; }
            }

            return null;
        }

/* methods */
        /**
         * Generate hashed password from a given clear password
         *
         * @param string $pwd clear password
         * @return string generated hashed password
         */
        public function generateHashedPassword(string $pwd): string {
            return password_hash($pwd, PASSWORD_DEFAULT); 
        }

        /**
         * Verify if a password matches a hash
         *
         * @param string $pwd clear password (unhashed)
         * @param string $pwdHash hashed password
         * @return boolean
         */
        public function verifyHashedPassword(string $pwd, string $pwdHash): bool {
            return password_verify($pwd, $pwdHash);
        }

        /**
         * Simple verification of the user table info (table name, column names)
         * Return true on success, false on failure
         * 
         * @return bool
         */
        private function verifyUserTableInfo(): bool {
            $query = "
                SELECT $this->userIdColumn, $this->userUsernameColumn, $this->userPasswordColumn
                FROM $this->userTable
                WHERE $this->userIdColumn IS NULL
            ";

            return $this->db->select($query);
        }

        /**
         * Attempt to authenticate a user using its credentials
         *
         * @param string $parLogin user's login
         * @param string $parPwd user's password
         * @return string user's ID
         */
        public function authenticate(string $parLogin, string $parPwd): string {
            $this->deauthenticate();
            $unHashedPwd = $parPwd;

            $query = "
                SELECT $this->userPasswordColumn hashedPwd, $this->userIdColumn id
                FROM $this->userTable
                WHERE $this->userUsernameColumn = :username
            ";

            $this->db->emptyParams();
            $this->db->addParamToBind('username', $parLogin);

            if ($this->db->select($query)) {
                if ($row = $this->db->getNextRow()) {
                    if ($this->verifyHashedPassword($unHashedPwd, $row['hashedPwd'])) {
                        $this->authenticated = true;
                        $this->userID = $row['id'];
                    }
                }
            }

            return $this->userID;
        }

        /**
         * Reset/update password.
         * Return true on success, false on failure
         *
         * @param string $parId user's ID
         * @param string $parUnHashedPwd user's clear password
         * @return boolean
         */
        public function resetPassword(string $parId, string $parUnHashedPwd): bool {
            $updated = false;

            $query = "
                UPDATE $this->userTable
                SET $this->userPasswordColumn = :password
                WHERE $this->userIdColumn = :id
            ";

            $this->db->emptyParams();
            $this->db->addParamToBind('id', $parId);
            $this->db->addParamToBind('password', $this->generateHashedPassword($parUnHashedPwd));

            if ($this->db->update($query)) { $updated = true; }

            return $updated;
        }
    }

