import axios from "axios";
import { defineStore } from "pinia";

export const useUserStore = defineStore("user", {
    state: () => ({
        currentUser: JSON.parse(localStorage.getItem("USER")),
        token: localStorage.getItem("TOKEN"),
        path:localStorage.getItem("PATH")
        
    }),
    actions: {
        async register(enterdUser) {
            var Token = {};
            var CurrentUser = null;
            var userType='';
            var Path=''
            await axios
                .post("http://localhost:8000/api/register", enterdUser, {
                    mode: "no-cors",
                    headers: {
                        "Access-Control-Allow-Origin": "*",
                        "Content-Type": "application/json",
                    },
                })
                .then(function (res) {
                    console.log(res);
                    if (res.data.status) {
                        console.log("errors: ", res.data.errors);
                    } else {
                        console.log(res.data.token);
                        Token = res.data.token;
                        CurrentUser = res.data.user;
                        userType = res.data.type
                        Path = res.data.path
                    }
                })
                .catch((e) => console.log("in action error:", e));
            this.token = Token;
            this.currentUser = CurrentUser;
            this.userType = userType
            this.path = Path
            localStorage.setItem("TOKEN", Token);
            localStorage.setItem("PATH", PATH);
            localStorage.setItem("USER", JSON.stringify(CurrentUser));
        },
        async login(enterdUser) {
            var Token = {};
            var CurrentUser = null;
            var userType='';
            var Path=''
            await axios
                .post("http://localhost:8000/api/login", enterdUser, {
                    mode: "no-cors",
                    headers: {
                        "Access-Control-Allow-Origin": "*",
                        "Content-Type": "application/json",
                    },
                })
                .then(function (res) {
                    console.log(res);
                    if (res.data.status) {
                        console.log("errors: ", res.data.errors);
                    } else {
                        console.log(res.data);
                        Token = res.data.token;
                        CurrentUser = res.data.user;
                        userType = res.data.type
                        Path = res.data.path
                    }
                })
                .catch((e) => console.log("in action error:", e));
            this.token = Token;
            this.currentUser = CurrentUser;
            this.path = Path
            localStorage.setItem("TOKEN", Token);
            localStorage.setItem("PATH", Path);
            localStorage.setItem("USER", JSON.stringify(CurrentUser));
        },
        logout() {
            this.currentUser = {};
            this.token = null;
            localStorage.removeItem("TOKEN");
        },
    },
});
