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
	</style>
</head>
<body>
	<div class="container-fluid">
		<div class="row">
			<h1 class="text-center">Wechat Monitor</h1>
		</div>
		<div class="row">
			<h2 class="text-center"><a href="">登陆</a>·<a href="">注册</a></h2>
		</div>
		<div class="row">
			<div class="col-md-4 col-sm-3">
			</div>
			<div class="col-md-4 col-sm-6">
				<form role="form" method="POST" action="/auth/login">
					<div class="form-group">
						<label for="exampleInputEmail1">邮箱</label>
						<input name="email" class="form-control"  placeholder="Email">
					</div>
					<div class="form-group">
						<label for="exampleInputPassword1">密码</label>
						<input name="password" class="form-control"  placeholder="Password">
					</div>
					<div>
						<input type="checkbox" name="remember"> Remember Me
						<button type="submit" class="btn btn-primary btn-lg btn-block">登陆</button>
					</div>
				</form>
			</div>
			<div class="col-md-4 col-sm-3">
			</div>
		</div>
	</div>
</body>
</html>