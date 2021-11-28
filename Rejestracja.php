<?php
ob_start();
session_start(); 
?>
 
<?php
 
if (!isset($_SESSION['login'])) { // dostęp dla zalogowanego użytkownika
 
    include 'inc/db.php'; // połączenie się z bazą danych
    $tabela = 'user'; // zdefiniowanie tabeli MySQL
 
if ($_POST["wyslane"]) { // jeżeli formularz został wysłany, to wykonuje się poniższy skrypt
 
        // filtrowanie treści wprowadzonych przez użytkownika
        $login = htmlspecialchars(stripslashes(strip_tags(trim($_POST["login"]))), ENT_QUOTES);
        $haslo = $_POST["haslo"];
        $haslo2 = $_POST["haslo2"];
        $email = htmlspecialchars(stripslashes(strip_tags(trim($_POST["email"]))), ENT_QUOTES);
 
        // system sprawdza czy prawidło zostały wprowadzone dane
        if (strlen($login) < 3 or strlen($login) > 30 or !eregi("^[a-zA-Z0-9_.]+$", $login)) {
            $blad++;
            echo '<span class="blad">Proszę poprawny wprowadzić login (od 3 do 30 znaków).</span>';
        } else {
            $wynik = mysql_query("SELECT * FROM $tabela WHERE login='$login'");
            if (mysql_num_rows($wynik) <> 0) {
                $blad++;
                echo '<span class="blad">Podana nazwa użytkownika została już zajęta.</span>';
            }
        }
        if (strlen($haslo) < 6 or strlen($haslo) > 30 ) {
            $blad++;
            echo '<span class="blad">Proszę poprawnie wpisać hasło (od 6 znaków do 30 znaków).</span>';
        }
        if ($haslo !== $haslo2) {
            $blad++;
            echo '<span class="blad">Podane hasła nie są ze sobą zgodne.</span>';
        }
        if (!eregi("^[0-9a-z_.-]+@([0-9a-z-]+\.)+[a-z]{2,4}$", $email)) {
            $blad++;
            echo '<span class="blad">Proszę wprowadzić poprawnie adres email.</span>';
        }
 
        // jeżeli nie ma żadnego błedu, użytkownik zostaje zarejestronwany i wysłany do niego e-mail z linkiem aktywacyjnym
        if ($blad == 0) {
 
            $haslo = md5($haslo); // zaszyfrowanie hasla
            $kod = uniqid(rand()); // tworzenie unikalnego kodu dla użytkownika
 
            $wynik = mysql_query("INSERT INTO $tabela VALUES('', '$id', '$login', '$haslo', '$email', '$kod', NOW(), '')");
            if ($wynik) {
                $list = "Witaj $login !
                Kliknij w poniższy link, aby aktywować swoje konto. <a href="http://www.nazwa-twojej-strony.pl/weryfikacja.php?weryfikacja=potwierdz&kod=$kod&quot;;" target="_blank">http://www.nazwa-twojej-strony.pl/weryfika...#036;kod";</a>
                mail($email, "Rejestracja użytkownika", $list, "From: <kontakt@twoja-strona.pl>");
                echo '<p>Dziękujemy za rejestrację! W ciągu nabliższych 5 minut dostaniesz wiadomość e-mail z dalszymi wskazówkami rejestracji.</p>';
                mysql_close($polaczenie);
                exit;
            }
        }
        mysql_close($polaczenie);
    }
 
    // tworzenie formularza HTML
    echo <<< KONIEC
 
    <div class="formularz">
    <form class="form" action="rejestracja.php" method="post">
    <input type="hidden" name="wyslane" value="TRUE" />
 
			<p>
				<div class="label"><label for="login">Login:</label></div>
				<input type="text" name="login" id="login" />
			</p>
 
			<p>
				<div class="label"><label for="haslo">Hasło:</label></div>
				<input type="password" name="haslo" id="haslo" />
			</p>
 
			<p>
				<div class="label"><label for="haslo2">Powtórz hasło:</label></div>
				<input type="password" name="haslo2" id="haslo2" />
			</p>
 
			<p>
				<div class="label"><label for="email">Email:</label></div>
				<input type="text" name="email" id="email" />
			</p>
   <p class="submit">
      <input type="submit" value="Zarejestruj mnie" />
   </p></form>
KONIEC;
 
} else {
    header('Location: /index.php');
}
?>
