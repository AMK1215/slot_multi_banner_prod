import "./bootstrap";

import Echo from "laravel-echo";
import Pusher from "pusher-js";

window.Pusher = Pusher;
window.Echo = new Echo({
    broadcaster: "pusher",
    key: process.env.MIX_PUSHER_APP_KEY,
    cluster: process.env.MIX_PUSHER_APP_CLUSTER,
    forceTLS: true,
});

window.Echo.private("App.Models.User." + userId).notification(
    (notification) => {
        $("#notificationCount").text(
            parseInt($("#notificationCount").text()) + 1
        );
        $(".dropdown-menu").prepend(
            `<li>
                <a href="#" class="dropdown-item">
                    <div class="d-flex">
                        <div class="flex-grow-1">
                            <h3 class="dropdown-item-title">${notification.player_name}</h3>
                            <p class="fs-7">${notification.message}</p>
                        </div>
                    </div>
                </a>
            </li>`
        );
    }
);
