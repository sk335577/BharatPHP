<?php

namespace BharatPHP\Session\Drivers;

use BharatPHP\Config;
use BharatPHP\Database\Connection;
use BharatPHP\Session\Drivers\Driver;
use PDO;

class Database extends Driver {

    /**
     * The database connection.
     *
     * @var Connection
     */
    protected $connection;

    /**
     * Create a new database session driver.
     *
     * @param  Connection  $connection
     * @return void
     */
    public function __construct($connection) {
        $this->connection = $connection;
    }

    /**
     * Load a session from storage by a given ID.
     *
     * If no session is found for the ID, null will be returned.
     *
     * @param  string  $id
     * @return array
     */
    public function load($id) {

        $statement = $this->connection->query('SELECT * FROM ' . $this->table());
        $session = $statement->fetch(PDO::FETCH_OBJ);

        if (!is_null($session) && !empty($session)) {
            return array(
                'id' => $session->id,
                'last_activity' => $session->last_activity,
                'data' => unserialize($session->data)
            );
        }
    }

    /**
     * Save a given session to storage.
     *
     * @param  array  $session
     * @param  array  $config
     * @param  bool   $exists
     * @return void
     */
    public function save($session, $config, $exists) {
        if ($exists) {

            $statement = $this->connection->prepare(' UPDATE ' . $this->table() . ' set last_activity=:last_activity, data=:data where id=:id ');

            $statement->execute([
                'id' => $session['id'],
                'last_activity' => $session['last_activity'],
                'data' => serialize($session['data']),
            ]);
        } else {
            $statement = $this->connection->prepare('INSERT INTO  ' . $this->table() . '  (id,last_activity,data) VALUES (:id,:last_activity,:data)');

            $statement->execute(array(
                'id' => $session['id'],
                'last_activity' => $session['last_activity'],
                'data' => serialize($session['data'])
            ));
        }
    }

    /**
     * Delete a session from storage by a given ID.
     *
     * @param  string  $id
     * @return void
     */
    public function delete($id) {
        $this->table()->delete($id);
    }

    /**
     * Delete all expired sessions from persistent storage.
     *
     * @param  int   $expiration
     * @return void
     */
    public function sweep($expiration) {
        $this->table()->where('last_activity', '<', $expiration)->delete();
    }

    /**
     * Get a session database query.
     *
     * @return Query
     */
    private function table() {
//        return $this->connection->table(Config::get('session.table'));
        return (Config::get('session.table'));
    }

}
