<p>
<?php
if (isset($_POST['login']) AND isset($_POST['pass']))
{
    $login = $_POST['login'];
    $pass_crypte = crypt($_POST['pass']); // On crypte le mot de passe
    echo $_SERVER['DOCUMENT_ROOT']."<br/>";
    
    echo 'Ligne à copier dans le .htpasswd :<br />' . $login . ':' . $pass_crypte;
}

else // On n'a pas encore rempli le formulaire
{
?>
</p>

<p>Entrez votre login et votre mot de passe pour le crypter.</p>

<form method="post">
    <p>
        Login : <input type="text" name="login"><br />
        Mot de passe : <input type="text" name="pass"><br /><br />
    
        <input type="submit" value="Crypter !">
    </p>
</form>

<?php
}
?>
