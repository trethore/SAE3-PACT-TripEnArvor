<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>
    <header>
        <!-- a inclure lorque le po aura fait son travail -->
    </header>

    <main>
        <h1> Creation d'une offre</h1>
        <form action = "creationoffre.php" method = "post" enctype="multipart/form-data">
            <h2>Informations importante</h2>
            <label>


            <label for="titre">Titre :</label> 
            <input type="text" id="titre" name ="titre" required>
            <br>
            <label for="categorie">Categorie :</label>
            <select id = "categorie" name = "lacat">
                <option value = "restaurant"> Restaurant</option>
                <option value = "parc"> Parc d'attraction</option>
                <option value = "spectacle"> Spectacle</option>
                <option value = "visite"> Visite</option>
                <option value = "activite"> Activité</option>
            </select>
            <br>
            <label for="dispo">Disponibilité :</label>
            <select id = "dispo" name = "ladispo">
                <option value = "ouvert"> Ouvert </option>
                <option value = "ferme"> Fermé </option>
            </select>
            <br>
            <label for= "adresse">Adresse</label>
            <input type="text" id="adresse" name ="adresse" required>


            <input type="submit" value="Envoyer">



    </main>

    <footer>
        <!-- a inclure lorque le po aura fait son travail -->
    </footer>

</body>
</html>