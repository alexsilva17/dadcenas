/*jshint esversion: 6 */

var app = require('http').createServer();

var io = require('socket.io')(app, {
    allowEIO3: true,
 cors: {
 origin: "http://localhost:8081",
 methods: ["GET", "POST"],
 credentials: true
 }
});

var LoggedUsers = require('./loggedusers.js');

app.listen(8080, function(){
    console.log('listening on *:8080');
});

let loggedUsers = new LoggedUsers();

io.on('connection', function (socket) {

    console.log('client has connected (socket ID = '+socket.id+')' );

    socket.on("user_enter", function(user) {
        if(user){
            loggedUsers.addUserInfo(user, socket.id);
        }
    });

    socket.on("expense_movement", function(expenseInfo) {

        if(expenseInfo){

            var userInfo = loggedUsers.getUsersInfoByEmail(expenseInfo.destination_email);
            let socket_id = userInfo !== undefined ? userInfo.socketID : null;
            
            if (socket_id !== null) {
                io.to(socket_id).emit("expense_movement", expenseInfo.value);
            }
            //emito sempre para quem faz a transferência, para atualizar o balanço e a lista de
            socket.emit("expense_movement", "me");

            if(expenseInfo.value > 1000){
                
                var admin = loggedUsers.getUsersInfoByEmail("admin1@mail.pt");
                let socket_id_admin = admin !== undefined ? admin.socketID : null;

                io.to(socket_id_admin).emit("money", expenseInfo.value);
            }

        }
    });

    socket.on("income_movement", function(incomeInfo) {

        if(incomeInfo){

            var userInfo = loggedUsers.getUsersInfoByEmail(incomeInfo.email);
            let socket_id = userInfo !== undefined ? userInfo.socketID : null;
            
            if (socket_id !== null) {
                io.to(socket_id).emit("income_movement", incomeInfo.value);
            }

            if(incomeInfo.value > 1000){
                
                var admin = loggedUsers.getUsersInfoByEmail("admin1@mail.pt");
                let socket_id_admin = admin !== undefined ? admin.socketID : null;

                io.to(socket_id_admin).emit("money", incomeInfo.value);
            }
        }
    });
});
