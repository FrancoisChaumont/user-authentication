<?php

namespace FC;

use FC\Db;
use FC\Authentication;

require __DIR__ . "/../vendor/autoload.php";

/* NOTE: 
    login credentials stored in the database are set like this: 
    login = username / password = username in lowercase (ex: MF / mf)
*/

// COMMON TO BOTH TESTS
// variables
    // database connection info 
    $dbname = 'tests_authclass';
    $host = 'localhost';
    $login = 'uauthclass';
    $password = 'uauthclasspassw';

// instantiation of a db object and attempt connection to the database
    echo '### <b>Connection to the database:</b> ';
    $db = new Db($dbname, $host, $login, $password);

    displayConnectionStatus($db->isConnected());
    echo "<br><br>";
    
    if (!($db->isConnected())) {
        echo $db->getErrMessage();
    }

// TESTS ON AUTH CLASS
echo "<font style='font-size:bigger'>AUTH TESTS</font><br>";
// variables
    // user table and column names
    $uTable = 'is_users';
    $uUsernameCol = 'username';
    $uPasswordCol = 'password';
    $uIdCol = 'id';

// instantiation of an auth object initializing its db member only with WRONG user table info
        echo "### <b>Error in user table info parameters:</b> ";
        $authGG = Authentication::newNoLogin($db, 'userTableWRONG', 'usernameWRONG', 'userPasswordWRONG', 'userIdWRONG');
        if ($authGG != null) { displayConnectionStatus($authGG->isConnected()); }
        else { echo "<font color='red'>object authGG NOT instantiated!</font>"; }

        echo "<br><br>";

// instantiation of an auth object initializing its db member only, then authenticate
        echo "### <b>Connection of object authGG to the database:</b> ";
        $authGG = Authentication::newNoLogin($db, $uTable, $uUsernameCol, $uPasswordCol, $uIdCol);
        displayConnectionStatus($authGG->isConnected());

        echo "<br>";

        echo "### <b>Authentication of object authGG using WRONG login/password for user GG:</b> ";
        $authGG->authenticate('GG', 'wrong');
        displayAuthenticationStatus($authGG->isAuthenticated());

        echo "<br>";

        echo "### <b>Authentication of object authGG using CORRECT login/password for user GG:</b> ";
        $authGG->authenticate('GG', 'gg');
        displayAuthenticationStatus($authGG->isAuthenticated());
        echo " / user ID: ".$authGG->getUserID();

        echo "<br><br>";

// instantiation of an auth object initializing its db member and attempting to authenticate it
        echo "### <b>Connection & authentication of object authMF using WRONG login/password for user MF:</b> ";
        $authMF = Authentication::newWithLogin($db, $uTable, $uUsernameCol, $uPasswordCol, $uIdCol, 'MF', 'wrong');
        if ($authMF != null) { 
            displayConnectionStatus($authMF->isConnected());
            echo " / ";
            displayAuthenticationStatus($authMF->isAuthenticated());
        }
        else { echo "<font color='red'>object authMF NOT instantiated!</font>"; }

        echo "<br>";

        echo "### <b>Connection & authentication of object authMF using CORRECT login/password for user MF:</b> ";
        $authMF = Authentication::newWithLogin($db, $uTable, $uUsernameCol, $uPasswordCol, $uIdCol, 'MF', 'mf');
        displayConnectionStatus($authMF->isConnected());
        echo " / ";
        displayAuthenticationStatus($authMF->isAuthenticated());
        echo " / user ID: ".$authMF->getUserID();
        echo "<br><br>";

// reset password for user TC
        echo "### <b>Reset password for user MF (new password 'fm' instead of 'mf'):</b> ";
        $user = 'MF';
        $newPwd = 'fm';
        $oldPwd = 'mf';
        $reset = $authMF->resetPassword(3, $newPwd);
        if (!($reset)) { 
            echo "<font color='red'>password NOT reset!</font>";
        }
        else { 
            echo "<font color='green'>password reset successfully!</font>"; 
            echo "<br>";
            echo "Verification of the new password set by authenticating with password 'fm': ";
            $authMF->authenticate($user, $newPwd);
            displayAuthenticationStatus($authMF->isAuthenticated());

            // restore of the initial password for test purpose
            $authMF->resetPassword(3, $oldPwd);
        }

    echo "<br><br>";


// FUNCTIONS
function displayPermission($parPermission) {
    $txtPermission = '';

    switch ($parPermission) {
        case ACL::AUTHORIZED: $txtPermission = 'AUTHORIZED'; break;
        case ACL::UNAUTHORIZED: $txtPermission = 'UNAUTHORIZED'; break;
        case ACL::INHERITED: $txtPermission = 'INHERITED'; break;
    }

    echo "<font style='color:purple'>".$parPermission.'</font> [ '.$txtPermission.' ]';
}

function displayConnectionStatus($connectionStatus) {
    if ($connectionStatus) { 
        echo "<font color='green'>CONNECTED</font>"; 
    }
    else { 
        echo "<font color='red'>CONNECTION FAILED</font>"; 
    }
}

function displayAuthenticationStatus($authenticationStatus) {
    if ($authenticationStatus) { 
        echo "<font color='green'>AUTHENTICATED</font>"; 
    }
    else { 
        echo "<font color='red'>AUTHENTICATION FAILED</font>"; 
    }
}

function var_dump_pre($mixed = null) {
    echo '<pre>';
    var_dump($mixed);
    echo '</pre>';
    return null;
}

function print_r_pre($mixed = null) {
    echo '<pre>';
    print_r($mixed);
    echo '</pre>';
    return null;
}

