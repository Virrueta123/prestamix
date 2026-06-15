#!/bin/bash

# Reemplaza "YOUR_AUTH_TOKEN" con tu token de ngrok
ngrok config add-authtoken 22hBVcUFnygXC9CXWdHp6Ezl9SX_4UgEAzEmLziTnZwzbeqqH


# Inicia ngrok en modo túnel con el subdominio "my-app"
ngrok http 80 -subdomain=my-app

# Mantén el script en ejecución en segundo plano
