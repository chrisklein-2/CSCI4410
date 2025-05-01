<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <!-- Font Awesome -->


    <!-- Fonts and Icons -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200&icon_names=menu" />
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css" rel="stylesheet">

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/mdb-ui-kit/7.3.2/mdb.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">

    <!-- Without integrity -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/mdb-ui-kit/7.3.2/mdb.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"></script>
    
    

    <!-- DataTables CSS and JS -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.0/css/jquery.dataTables.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.3.6/css/buttons.dataTables.min.css">
   
    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-lpy1eF4pjPpk9tgA8BQ8R3tJbOZn29NtaV5XBwWihVoK84Gu5G3afSUnC1Dz4m8G" crossorigin="anonymous"></script>
    <!-- Custom CSS -->
    <link rel="stylesheet" href="./Assets/css/main.css">

    
    <style>
        .btn{
          white-space: nowrap;
        }
        body>.display-table-row{
          position: relative;
        }
        .dashboard-footer{
          position:fixed;
          bottom:0;
          margin-left:280px;
        }
        .dashboard-sidebar{
          position: absolute;
          left:0;
          width: 250px;
        }
        .dashboard-main{
          margin-left:250px;
          left:0;
        }
        .chart-container{
                display:flex;
                justify-content:flex-start;
                gap:100px;
            }
            .chart{
                width:100%;
                height:600px;
            }

        .form-check-input[type=radio]:checked:after {
            border-radius: 50%;
            width: .625rem;
            height: .625rem;
            border-color: $primary;
            background-color: $primary;
            transition: border-color;
            transform: translate(-50%, -50%);
            position: absolute;
            left: 15.5%;
            top: 50%;
          }
          .form-outline .form-control{
            border: 1px solid #ccc;

          }
          .form-outline .form-control::placeholder{
            color:  #ccc;
            
          }

    </style>
</head>
<body>
   