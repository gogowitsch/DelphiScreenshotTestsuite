<?PHP

require_once '../include/queue.inc.php';

db_connect("CREATE TABLE IF NOT EXISTS `subscribers` (" .
           "`project` VARCHAR(255),".
           "`email` VARCHAR(255),".
           "`ID` INT NOT NULL AUTO_INCREMENT PRIMARY KEY);");

function getSubscribers($project) {
  global $conn;
  $project = $conn->quote($project);
  return db_connect("SELECT * FROM subscribers WHERE project=$project");
}
