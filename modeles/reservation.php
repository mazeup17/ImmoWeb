<?php
class reservation
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

    public function getReservation($id)
    {
        $sql = $this->pdo->prepare("SELECT * from reservations WHERE ID_Appartement = :id");
        $sql->bindParam(':id', $id, PDO::PARAM_STR);
        $sql->execute();
        $res = $sql->fetchAll(PDO::FETCH_ASSOC);
        return $res;
    }

    public function getReservationUser($idUser)
    {
        $sql = $this->pdo->prepare("SELECT * from reservations WHERE ID_Utilisateur = :id");
        $sql->bindParam(':id', $idUser, PDO::PARAM_STR);
        $sql->execute();
        $res = $sql->fetchAll(PDO::FETCH_ASSOC);
        return $res;
    }

    public function ajoutReservation($idUtilisateur, $idReserv, $idAppart, $prix, $dateDebut, $dateFin)
    {
        $sql = $this->pdo->prepare("UPDATE periodes_de_reservation SET statut = 'réservé' WHERE ID=:id");
        $sql->bindParam(":id", $idReserv, PDO::PARAM_INT);
        $reussi = $sql->execute();

        if ($reussi) {
            $sql = $this->pdo->prepare("INSERT INTO reservations(ID_Utilisateur, ID_Appartement, Prix, DateDebut, DateFin, Statut) VALUES(:idUtil, :idAppart, :prix, :dateDebut, :dateFin, 'reservé')");
            $sql->bindParam(":idUtil", $idUtilisateur, PDO::PARAM_INT);
            $sql->bindParam(":idAppart", $idAppart, PDO::PARAM_INT);
            $sql->bindParam(":prix", $prix, PDO::PARAM_STR);
            $sql->bindParam(":dateDebut", $dateDebut, PDO::PARAM_STR);
            $sql->bindParam(":dateFin", $dateFin, PDO::PARAM_STR);
            return $sql->execute();
        }

    }

    public function annuleReservation($idUtilisateur)
    {
        $sql = $this->pdo->prepare("UPDATE reservations SET Statut='libre' WHERE id_Utilisateur = :idUtil");
        $sql->bindParam(":idUtil", $idUtilisateur, PDO::PARAM_INT);
        return $sql->execute();
    }



}


?>