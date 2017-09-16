<html>
 <head>
  <title>G9 Telecom - CDRs Statistics</title>
 </head>
<style>

        html {
          font-family: sans-serif;
        }

          table {
          border-collapse: collapse;
          border: 2px solid rgb(200,200,200);
          letter-spacing: 1px;
          font-size: 0.8rem; 
        }

        td, th {
          border: 1px solid rgb(190,190,190);
          padding: 10px 20px;
        }

        th {
          background-color: rgb(235,235,235);
        }

        td {
          text-align: center;
        }

        tr:nth-child(even) td {
          background-color: rgb(250,250,250);
        }

        tr:nth-child(odd) td {
          background-color: rgb(245,245,245);
        }

        caption {
          padding: 10px;
        }

        tbody {
          font-size: 90%;
        }

        tfoot {
          font-weight: bold;
        }
    </style>


 <body>
 <?php 
 $locale_db_host  = "localhost";
 $locale_db_name  = "asteriskcdrdb";
 $locale_db_login = "root";
 $locale_db_pass  = "iuRtujehGe1";
 
 // Create database
 /*$sql = "CREATE DATABASE G9Telecom";
 if (mysqli_query($conn, $sql)) {
     echo "Database created successfully";
 } else {
     echo "Error creating database: " . mysqli_error($conn);
 }*/
 
 // connect to db
 $conn = mysqli_connect($locale_db_host, $locale_db_login, $locale_db_pass, $locale_db_name);
 
 // Check connection
 if ($conn->connect_error) {
     die("Connection failed: " . $conn->connect_error);
 }
 //echo "Connected successfully";
 
 // Create table
 /*$sql = "CREATE TABLE cdr ( 
        calldate datetime NOT NULL default '0000-00-00 00:00:00', 
        src varchar(80) NOT NULL default '', 
        dst varchar(80) NOT NULL default '', 
        duration int(11) NOT NULL default '0', 
        billsec int(11) NOT NULL default '0',
        disposition varchar(45) NOT NULL default ''
);";
 
 if (mysqli_query($conn, $sql)) {
     echo "Table created successfully";
 } else {
     echo "Error creating table: " . mysqli_error($conn);
 }*/
 
 
 // Estatisticas - primeira tabela

 $sql = "SELECT dst, count(dst) AS chamadas, SUM(duration) as tempo, round(avg(billsec != 0)*100,1) as atendidas FROM cdr WHERE dst > 100 AND dst < 500 GROUP BY dst ORDER BY dst DESC";
 
 $result = $conn->query($sql);
 
$sql1 = "SELECT src, count(src) AS chamadas, SUM(duration) as tempo, round(avg(billsec != 0)*100,1) as efectuadas FROM cdr WHERE src > 100 AND src < 500 GROUP BY src ORDER BY src DESC";

 $result1 = $conn->query($sql1);

 if ($result->num_rows > 0) {
     echo "<table border=\"5\"><tr><th>Extensão</th><th>Nº Chamadas</th><th>Tempo total</th><th>% Atendidas</th><th>Duração média</th><th>Nº chamadas</th><th>Tempo total</th><th>% efectuadas</th><th>Duração média</th><th>Nº tentativas</th><th>%</th></tr>";
     
	$total_chamadas = 0;
	$total_tempo = 0;
	$total_perc_atendidas = 0;
	$total_duracao_media_chamadas = 0;
	$total_num_chamadas = 0;
	$total_tempo_parcial = 0;
	$total_perc_efectuadas = 0;
	$total_duracao_media_efectuadas = 0;
	$total_num_tentativas = 0;
	$total_percentage = 0;

	// output data of each row
     while(($row = $result->fetch_assoc()) && ($row1 = $result1->fetch_assoc())) {
         echo "<tr align=center><td>".$row["dst"]."</td><td>".$row["chamadas"]."</td><td>".$row["tempo"]." seg"."</td><td>".$row["atendidas"]."</td><td>".round($row["tempo"]/$row["chamadas"])."</td><td>".$row1["chamadas"]."</td><td>".$row1["tempo"]." seg"."</td><td>".$row1["atendidas"]."</td><td>".round($row1["tempo"]/$row1["chamadas"])."</td></tr>";
  	$total_chamadas += $row["chamadas"];
        $total_tempo += $row["tempo"];
        $total_perc_atendidas += $row["atendidas"];
        $total_duracao_media_chamadas += round($row["tempo"]/$row["chamadas"]);
        $total_num_chamadas += $row["carga"];
        $total_tempo_parcial += $row["duracao"];
	$total_perc_efectuadas += $row["nao_atendidas_percentage"];
        $total_duracao_media_efectuadas += $row["carga"];
        $total_num_tentativas += $row["duracao"];     
	$total_percentage += $row["duracao"];
	}
  echo "<tr align=center><td>"."Total"."</td><td>".$total_chamadas."</td><td>".$total_tempo." seg"."</td><td>"." "."</td><td>".round(($total_duracao_media_chamadas/$result->num_rows),1)."</td><td>".$total_num_chamadas."</td><td>".$total_tempo_parcial."</td><td>".$total_perc_efectuadas."</td><td>".$total_duracao_media_efectuadas."</td><td>".$total_num_tentativas."</td><td>".$total_percentage."</td></tr>";
     echo "</table>";
 } else {
     echo "0 results";
 }
 
 echo "<br>";
 
 // Estatisticas - segunda tabela
 
 $sql = "SELECT hour(calldate) as horas, sum(billsec != 0) as atendidas, avg(billsec != 0)*100 as atendidas_percentage, sum(billsec = 0) as nao_atendidas,avg(billsec = 0)*100 as nao_atendidas_percentage,
(count(*)/(Select count(*) from cdr))  as carga, avg(duration) as duracao FROM cdr GROUP BY hour( calldate )";
 
 $result = $conn->query($sql);
 
 if ($result->num_rows > 0) {
     echo "<table border=\"5\"><tr><th>Horas</th><th>Atendidas</th><th>%Atendidas</th><th>Não Atendidas</th><th>% Não Atendidas</th><th>% de Carga</th><th>Duração Média</th></tr>";
	$total_atendidas = 0;
        $total_perc_atendidas = 0;
	$total_nao_atendidas = 0;
	$total_perc_nao_atendidas = 0;
	$total_perc_carga = 0;
	$total_duracao_media = 0;

	// output data of each row
     while($row = $result->fetch_assoc()) {
         echo "<tr align=center><td>".$row["horas"]."</td><td>".$row["atendidas"]."</td><td>".round($row["atendidas_percentage"])."</td><td>".$row["nao_atendidas"]."</td><td>".round($row["nao_atendidas_percentage"],1)."</td><td>".round($row["carga"],1)."</td><td>".round($row["duracao"])."</td></tr>";
     	$total_atendidas += $row["atendidas"]; 
        $total_perc_atendidas += $row["atendidas_percentage"];
	$total_nao_atendidas += $row["nao_atendidas"];
	$total_perc_nao_atendidas += $row["nao_atendidas_percentage"];
	$total_perc_carga += $row["carga"];
	$total_duracao_media += $row["duracao"];	
	}
     echo "<tr align=center><td>"."Total"."</td><td>".$total_atendidas."</td><td>".round($total_perc_atendidas/$result->num_rows,1)."</td><td>".$total_nao_atendidas."</td><td>".round($total_perc_nao_atendidas,1)."</td><td>".round($total_perc_carga,1)."</td><td>".round($total_duracao_media,1)."</td></tr>";
     echo "</table>";
 } else {
     echo "0 results";
 }
 
 echo "<br>";
 // Estatisticas - terceira tabela
 
 $sql = "SELECT count(*) as atendidas FROM cdr where duration > 0 AND duration <21;";
 
 $result = $conn->query($sql);

$row = $result->fetch_assoc();

$sql = "SELECT count(*) as atendidas FROM cdr where duration > 24 AND duration < 56;";

 $result = $conn->query($sql);

$row1 = $result->fetch_assoc();

$sql = "SELECT count(*) as atendidas FROM cdr where duration > 59 AND duration < 111;";

 $result = $conn->query($sql);

$row2 = $result->fetch_assoc();

$sql = "SELECT count(*) as total FROM cdr;";

 $result = $conn->query($sql);

$row3 = $result->fetch_assoc();
 
 echo "<table border=\"5\"><tr><th>Mensagem</th><th>Atendidas</th><th>% Atendidas Comulativas</th><th>Abandonos</th><th>% Abandonos</th></tr>";
 echo "<tr align=center><td>".    "[0,20]s"   ."</td><td>".$row["atendidas"]."</td><td>".($row["atendidas"]/$row3["total"])."</td><td>".$row["nao_atendidas"]."</td><td>".$row["nao_atendidas_percentage"]."</td></tr>";
 echo "<tr align=center><td>".    "1ª Mensagem"   ."</td><td>"."-"."</td><td>"."-"."</td><td>".$row["nao_atendidas"]."</td><td>".$row["nao_atendidas_percentage"]."</td></tr>";
 echo "<tr align=center><td>".    "[25,55]s"   ."</td><td>".$row1["atendidas"]."</td><td>".(($row["atendidas"]+$row1["atendidas"])/$row3["total"])."</td><td>".$row["nao_atendidas"]."</td><td>".$row["nao_atendidas_percentage"]."</td></tr>";
 echo "<tr align=center><td>".    "2ª Mensagem"   ."</td><td>"."-"."</td><td>"."-"."</td><td>".$row["nao_atendidas"]."</td><td>".$row["nao_atendidas_percentage"]."</td></tr>";
 echo "<tr align=center><td>".    "[60,110]s"   ."</td><td>".$row2["atendidas"]."</td><td>".(($row["atendidas"]+$row1["atendidas"]+$row2["atendidas"])/$row3["total"])."</td><td>".$row["nao_atendidas"]."</td><td>".$row["nao_atendidas_percentage"]."</td></tr>";
 echo "<tr align=center><td>".    "Não Atendidas (VoiceMail)"   ."</td><td>"."-"."</td><td>"."-"."</td><td>".$row["nao_atendidas"]."</td><td>".$row["nao_atendidas_percentage"]."</td></tr>";
 echo "<tr align=center><td>"."Total"."</td><td>".($row["atendidas"]+$row1["atendidas"]+$row2["atendidas"])."</td><td>"."-"."</td><td>".$row["nao_atendidas"]."</td><td>".$row["nao_atendidas_percentage"]."</td></tr>";
 
 
 mysqli_close($conn);
 ?> 
 </body>
</html>
