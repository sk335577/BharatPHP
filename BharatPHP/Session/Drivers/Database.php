<?php

namespace BharatPHP\Session\Drivers;

use BharatPHP\Config;
use BharatPHP\Database\Connection;
use BharatPHP\Session\Drivers\Driver;
use PDO;
use BharatPHP\Session\Drivers\Sweeper;

class Database extends Driver implements Sweeper
{

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
    public function __construct($connection)
    {
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
    public function load($id)
    {

        $statement = $this->connection->query('SELECT * FROM ' . config('session.table') . ' WHERE id="' . $id . '"');
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
    public function save($session, $config, $exists)
    {
        if ($exists) {

            $statement = $this->connection->prepare(' UPDATE ' . config('session.table') . ' set last_activity=:last_activity, data=:data where id=:id ');

            $statement->execute([
                'id' => $session['id'],
                'last_activity' => $session['last_activity'],
                'data' => serialize($session['data']),
            ]);
        } else {
            $statement = $this->connection->prepare('INSERT INTO  ' . config('session.table') . '  (id,last_activity,data) VALUES (:id,:last_activity,:data)');

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
    public function delete($id)
    {
        $this->table()->delete($id);
    }

    /**
     * Delete all expired sessions from persistent storage.
     *
     * @param  int   $expiration
     * @return void
     */
    public function sweep($expiration)
    {
        $statement = $this->connection->prepare('DELETE FROM  ' . config('session.table') . ' WHERE last_activity<:expiration');

        $statement->execute(array(

            'expiration' => $expiration,

        ));
        // $this->table()->where('last_activity', '<', $expiration)->delete();
    }

    /**
     * Get a session database query.
     *
     * @return Query
     */
    private function table()
    {
        return $this->connection->table(config('session.table'));
    }
}
