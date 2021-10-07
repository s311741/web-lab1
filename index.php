<?php $timer_start = microtime(true); ?>

<!DOCTYPE HTML PUBLIC>
<html>
	<head>
		<meta charset="utf-8">
		<title>lab1</title>

		<style>
			.title {
				font-family: serif;
				font-size: 120%;
				color: black;
			}
			.small-print {
				font-size: 70%;
			}

			button:hover, input:hover {
				background: lightgrey;
			}

			.radio {
				border: 1px solid grey;
				border-radius: 30%;
				background: lightgrey;
			}

			.content {
				max-width: 600px;
				margin: auto;
			}

			table {
				text-align: center;
			}

			#history-body {
				background: lightgrey;
			}
			#history-head {
				background: grey;
				color: white;
			}

		</style>
	</head>

	<body>
		<div class="content">
			<div class="title">
				Соколов Иван Денисович P3214<br>
				Вариант 14208
			</div>

			<canvas id="canvas" width="600" height="400"></canvas>
			<br>

			<?php
				$x = $_GET["x"] ?: 0;
				$y = $_GET["y"] ?: 0;
				$r = $_GET["r"] ?: 1;

				$hit = false;
				$correct = true;

				if ($x < -3 || $x > 5 || $y < -5 || $y > 5) {
					echo "Точка (".$x.", ".$y.") задана некорректно";
					$correct = false;
				}

				if ($r < 0 || $r > 4) {
					echo "Радиус ".$r." задан некорректно";
					$correct = false;
				}

				if ($correct) {
					$hit = ($x >= -$r && $x <= 0 && $y >= 0 && $y <= $r)
					    || ($x >= 0 && $y >= 0 && ($x*$x + $y*$y <= $r*$r))
					    || ($x <= 0 && $y <= 0 && (-$r/2 - $x <= $y)) ? 1 : 0;
					echo "Точка (".$x.", ".$y.") ".($hit ? "" : "не ")
					  ." попадает в закрашенную область при радиусе ".$r.".";
				}
			?>

			<form name="params" method="get" onsubmit="return validate();">
				<table>
					<tr>
						<td>Изменение X</td>
						<td>
							<?php
								for ($i = -3; $i <= 5; $i++)
									echo "<float class=\"radio\"><input type=\"radio\" name=\"x\" value=".$i.">".$i."</float>"
							?>
						</td>
					</tr>
					<tr>
						<td>Изменение Y</td>
						<td><input type="text" name="y"></td>
					</tr>
					<tr>
						<td>Радиус</td>
						<td><input type="text" name="r"></td>
					</tr>
					<tr><td/><td><input type="submit"></td></tr>
				</table>
			</form>

			История:
			<button type="reset" onclick="return clearHistory()">Очистить</button>
			<table id="history-table">
				<thead id="history-head">
					<tr>
						<td>X</td>
						<td>Y</td>
						<td>Радиус</td>
						<td>Попадание</td>
					</tr>
				</thead>
				<tbody id="history-body"></tbody>
			</table>

			<script>
				{
					// Draw shape
					const cv = document.getElementById("canvas");
					const cx = cv.getContext("2d");
					const w = cv.width;
					const h = cv.height;
					const scale = h/10;

					const r = <?php echo $r; ?> * scale;
					const x = w/2;
					const y = h/2;

					cx.fillStyle = "#0000BB";
					cx.beginPath();
					cx.moveTo(x, y);
					cx.arc(x, y, r, 0, Math.PI/2);
					cx.fill();

					cx.fillRect(x-r, y, r, r);

					cx.beginPath();
					cx.moveTo(x, y);
					cx.lineTo(x-r/2, y);
					cx.lineTo(x, y-r/2);
					cx.fill();

					cx.beginPath();
					cx.moveTo(x, 0); cx.lineTo(x, h);
					cx.moveTo(0, y); cx.lineTo(w, y);
					cx.stroke();

					<?php echo "const xx = ".$x."; const yy = ".$y.";" ?>

					cx.fillStyle = "#00BBBB";
					cx.beginPath();
					cx.arc(x + scale*xx, y + scale*yy, 5, 0, Math.PI * 2);
					cx.fill();
				}

				<?php if ($correct): ?>
				{
					// Push to storage
					let arr = localStorage.getItem("queries") || "[]";
					arr = JSON.parse(arr);
					arr.push({ <?php echo "x: ".$x.", y: ".$y.", r: ".$r.", hit: ".$hit; ?> });
					localStorage.setItem("queries", JSON.stringify(arr));
				}
				<?php endif ?>

				{
					// Generate history table
					const table = document.getElementById("history-body");
					const arr = localStorage.getItem("queries");
					if (arr !== null) {
						for (const q of JSON.parse(arr)) {
							const row = document.createElement("tr");
							table.appendChild(row);

							let tds = { };
							for (const [k, v] of Object.entries(q))
								(tds[k] = document.createElement("td")).innerHTML = v.toString();
							tds.hit.innerHTML = q.hit ? "Да" : "Нет";

							for (const v of Object.values(tds))
								row.appendChild(v);
						}
					}
				}

				function validate () {
					const form = document.forms["params"];
					const y = +form["y"].value;
					const r = +form["r"].value;
					if (isNaN(y) || y < -5 || y > 5)
						return false;
					if (isNaN(r) || r < 1 || r > 4)
						return false;
					return true;
				}

				function clearHistory () {
					localStorage.setItem("queries", "");
					window.location.reload();
					return false;
				}
			</script>

			<div class="small-print">
				<?php
					echo "Сгенерировано за ".number_format(1000 * (microtime(true) - $timer_start), 5)." мс. ";
					echo "Время сервера: ".date("Y-m-d H:i:s")
				?>
			</div>
		</div>
	</body>
</html>