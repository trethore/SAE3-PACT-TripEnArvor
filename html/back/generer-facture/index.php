<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Facture</title>
    <link rel="stylesheet" href="/style/style.css">
</head>
<body class="genFacture">
    <img src="/images/universel/logo/Logo_couleurs.png" alt="logo de PACT">
    <section>
        <article class="delivre">
            <h3>Délivré à</h3>
            <p>
                {Nom} {Prénom}<br>
                {Dénomination sociale}<br>
                mail@example.com
            </p>
        </article>
        <article>
            <h3>Numéro de facture</h3>
            <h3>#012345</h3>
            <p>{jj/mm/aaaa}</p>
        </article>
    </section>
    <section class="facture-details">
        <table>
            <thead>
                <tr>
                    <th>Description</th>
                    <th>Date</th>
                    <th>Qté</th>
                    <th>% TVA</th>
                    <th>Prix HT</th>
                    <th>Prix Unitaire</th>
                    <th>Prix TTC</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>Option relief</td>
                    <td>15/04/2024</td>
                    <td>2</td>
                    <td>20%</td>
                    <td>5.10€</td>
                    <td>6.50€</td>
                    <td>22.30€</td>
                </tr>
                <tr>
                    <td>Offre premium</td>
                    <td>17/04/2024</td>
                    <td>1</td>
                    <td>20%</td>
                    <td>24.75€</td>
                    <td>24.75€</td>
                    <td>5.10€</td>
                </tr>
            </tbody>
        </table>
    </section>
    <hr>
    <section class="totals">
        <article>
            <span>Total HT : </span>
            <p>31.25€</p>
        </article>
        <article>
            <span>Total TVA : </span>
            <span>10€</span>
        </article>
        <article>
            <span>Total TTC : </span>
            <span>34.38€</span>
        </article>
    </section>
    <article class="payment-terms">
        <h3>Conditions et modalités de paiement</h3>
        <p>Le paiement est dû dans les 15 jours</p>
        <p>
            Nom Banque<br>
            Nom du compte: Nom<br>
            Numéro de compte : 123-456-7890
        </p>
    </article>
</body>
</html>
