<?php declare(strict_types=1);
use PHPUnit\Framework\TestCase;

require ("./modeles/utilisateurs.php");

final class UtilisateursTest extends TestCase
{
    public function testExistEmail()
    {
        $mail = 'pourletest@gmail.com';

        $utilisateurs = new Utilisateurs();

        $result = $utilisateurs->existEmail($mail);
        var_dump($result);


        $this->assertTrue($result, 'La connexion a échoué');

    }
}
