<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
    <title>Login</title>
	<link rel="stylesheet" href="backend/css/bootstrap.min.css">
	<style>
		html, body {
			height: 85%;
		}

		body {
			margin: 0;
			padding: 0;
			width: 100%;
			display: table;
			font-weight: 100;
			font-family: 'Lato';
		}

		.container-fluid {
			text-align: left;
			display: table-cell;
			vertical-align: middle;
		}
		#title{
			margin-bottom: 30px;
		}
	</style>
</head>
<body>
	<div class="container-fluid">
		<div class="row">
			<h1 class="text-center" id="title">Login</h1>
		</div>
		<div class="row">
			<div class="col-md-4 col-sm-3">
			</div>
			<div class="col-md-4 col-sm-6">
				<form role="form">
					<div class="form-group">
						<label for="exampleInputEmail1">用户</label>
						<input type="email" class="form-control" id="exampleInputEmail1" placeholder="Username">
					</div>
					<div class="form-group">
						<label for="exampleInputPassword1">密码</label>
						<input type="password" class="form-control" id="exampleInputPassword1" placeholder="Password">
					</div>
					<button type="submit" class="btn btn-primary btn-lg btn-block">登陆</button>
				</form>
			</div>
			<div class="col-md-4 col-sm-3">
			</div>
		</div>
	</div>
</body>
</html>