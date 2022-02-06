mercure_server:
	./bin/mercure --jwt-key='!ChangeMe!' --addr='localhost:3000' --demo='1' --debug --allow-anonymous='1' --cors-allowed-origins='*' --publish-allowed-origins='http://localhost:3000/.well-known/mercure'
