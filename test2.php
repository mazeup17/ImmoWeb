<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Réservation</title>

    <!-- Inclure les fichiers CSS de Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>

<div class="container mt-5">
    <h1 class="mb-4">Sélectionnez une date de réservation</h1>

    <div class="form-group">
        <label for="reservationDate">Date de réservation :</label>
        <select class="form-control" id="reservationDate">
            <option value="" disabled selected>Sélectionnez une date</option>
            <option value="2023-12-14">14 décembre 2023</option>
            <option value="2023-12-15">15 décembre 2023</option>
            <option value="2023-12-16">16 décembre 2023</option>
            <!-- Ajoutez d'autres dates disponibles selon vos besoins -->
        </select>
    </div>

    <button class="btn btn-primary" id="btnReserver">Réserver</button>
</div>

<!-- Inclure les fichiers JavaScript de Bootstrap (jQuery nécessaire) -->
<script src="https://code.jquery.com/jquery-3.6.4.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.min.js"></script>

</body>
</html>
