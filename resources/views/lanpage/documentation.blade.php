<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>dARK Documentation</title>
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f4f4f4;
            display: flex;
        }
        .sidebar {
            width: 250px;
            background: #333;
            color: #fff;
            height: 100vh;
            position: fixed;
            padding-top: 20px;
            overflow-y: auto;
        }
        .sidebar h2 {
            text-align: center;
            color: #fff;
        }
        .sidebar ul {
            list-style: none;
            padding: 0;
        }
        .sidebar ul li {
            padding: 10px;
            text-align: center;
        }
        .sidebar ul li a {
            color: #fff;
            text-decoration: none;
            display: block;
        }
        .sidebar ul li a:hover {
            background: #575757;
        }
        .submenu {
            display: none;
            padding-left: 20px;
        }
        .submenu li {
            text-align: left;
        }
        .content {
            margin-left: 250px;
            padding: 20px;
            background: #fff;
            flex: 1;
        }
        h1, h2, h3 {
            color: #333;
        }
        .text-justify {
            text-align: justify;
        }
        .center-image {
            display: block;
            margin-left: auto;
            margin-right: auto;
        }
    </style>
</head>
<body>
   
    @include('lanpage.documentation.menu')
    <div class="content">
        @include('lanpage.documentation.concepts')
        @include('lanpage.documentation.getstarted')
        @include('lanpage.documentation.ournetwork')
       
        @include('lanpage.documentation.tutorials')
        @include('lanpage.documentation.faq')       
    </div>

    <script>
        function toggleMenu(id) {
            var menu = document.getElementById(id);
            if (menu.style.display === "block") {
                menu.style.display = "none";
            } else {
                menu.style.display = "block";
            }
        }
    </script>
</body>
</html>
