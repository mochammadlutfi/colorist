import Pusher from "pusher-js";
import Cookies from "js-cookie";


const pusher = new Pusher("99920a495678463510cf", {
    cluster: "ap1",
    encrypted: true,
    authEndpoint: "/broadcasting/auth",
    auth: {
        headers: {
            Authorization: `Bearer ${Cookies.get("token")}`, // Jika pakai token JWT
        },
    },
});

export default pusher;
