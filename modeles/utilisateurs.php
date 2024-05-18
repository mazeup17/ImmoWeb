<?php

class Utilisateurs
{
    private $pdo;

    public function __construct()
    {

        try {
            $infoConnexion = parse_ini_file("base.ini");
            $this->pdo = new PDO("mysql:host=" . $infoConnexion["host"] . ";dbname=" . $infoConnexion["database"] . ";charset=utf8", $infoConnexion["user"], $infoConnexion["password"]);
            $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $e) {
            echo $e->getMessage();
        }
    }

    public function existEmail($email)
    {
        $sql = $this->pdo->prepare("SELECT * from utilisateurs WHERE Email=:email");
        $sql->bindParam(':email', $email, PDO::PARAM_STR);
        $sql->execute();
        if ($sql->rowCount() > 0) {
            return true;
        } else {
            return false;
        }

    }

    public function inscription($nom, $prenom, $mail, $mdp)
    {
        if (!$this->existEmail($mail)) {
            $hash = password_hash($mdp, PASSWORD_DEFAULT);
            $sql = $this->pdo->prepare("INSERT INTO Utilisateurs(Nom, Prenom, Email, MotDePasse, Role) values(:nom, :prenom, :mail, :mdp, 'client')");

            $sql->bindParam(':nom', $nom, PDO::PARAM_STR);
            $sql->bindParam(':prenom', $prenom, PDO::PARAM_STR);
            $sql->bindParam(':mail', $mail, PDO::PARAM_STR);
            $sql->bindParam(':mdp', $hash, PDO::PARAM_STR);
            if ($sql->execute()) {
                return true;
            } else {
                return false;
            }
        } else {
            return false;
        }


    }



    public function connexion($email, $motDePasse)
    {

        $stmt = $this->pdo->prepare("SELECT ID, MotDePasse, Role, Nom, Prenom FROM Utilisateurs WHERE Email = :email");
        $stmt->bindParam(':email', $email, PDO::PARAM_STR);
        $stmt->execute();
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if (password_verify($motDePasse, $user['MotDePasse'])) {

            $_SESSION['connexion'] = $user['ID'];
            $_SESSION['role'] = $user["Role"];
            $_SESSION['user'] = $user['Nom'] . " " . $user['Prenom'];
            return true;
        }

        return false;
    }

    public function getInfo($id)
    {
        $stmt = $this->pdo->prepare("SELECT * FROM Utilisateurs WHERE ID = :id");
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }





    public function deconnexion()
    {

        session_destroy();

    }

}

?>