/*jshint esversion: 6 */

class LoggedUsers {
	constructor() {
        this.users = new Map();
    }

    addUserInfo(user, socketID) {
        let userInfo = {user: user, socketID: socketID};
    	this.users.set(user.id, userInfo);
    	return userInfo;
    }

    getUsersInfoByEmail(email) {
        for (var [userID, userInfo] of this.users) {
            if (userInfo.user.email == email)  {
                return userInfo;    
            }
        }
        return null;
    }
}

module.exports = LoggedUsers;
