[phases.setup]
nixPkgs = ["php82", "php82Extensions.mysqli", "php82Extensions.pdo_mysql"]

[phases.build]
cmds = []

[start]
cmd = "apache2-foreground"