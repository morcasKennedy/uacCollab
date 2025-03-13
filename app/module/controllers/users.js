import * as fx from '../functions/functions.js';

$(document).ready(()=> {
    // log all users
    $(document).on('click', '#login', async (e) => {
        e.preventDefault();

        const data = {
            email: fx.get_value('email'),
            password: fx.get_value('password'),
            action: 'login',
        };
        const url = fx.get_controller_url('api');
        const status = await fx.save(data, url);

        if (status) {
            setInterval(()=> {
                fx.redirect('./');
            }, 2000);
        }
    });
});