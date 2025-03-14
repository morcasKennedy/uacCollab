<style>
    .password-container {
    position: relative;
    display: inline-block;
    width: 100%;
}

.password-container input {
    padding-right: 35px; /* Pour laisser de la place à l'icône */
    width: 100%;
}

.eye-icon {
    position: absolute;
    right: 10px;
    top: 50%;
    transform: translateY(-50%);
    cursor: pointer;
    font-size: 18px;
}

.img-collab {
    width: 40px;
    height: 40px;
    object-fit: cover;
    border-radius: 50%;
    margin-right: 10px;
}

.floating-btn {
    width: 50px;
    height: 50px;
    border-radius: 50%;
    display: flex;
    position: fixed;
    background: #3498DB;
    color: #fff;
    bottom: 20px;
    right: 20px;
    cursor: pointer;
    align-items: center;
    justify-content: center;
    font-size: 30px;
    font-weight: bold;
}

.bg-img {
    /* background: green; */
    width: 100%;
    min-height: 80px;
    background-position: center;
    background-size: cover;
    border-radius: 6px;
    display: flex;
    color: #fff;
    /* background-image: linear-gradient(rgba(0, 0, 0, 0.6), rgba(0, 0, 0, 0.6)), url('assets/themes/uac.png'); */
}

.img,
.img2 {
    margin-top: -30px;
    width: 60px;
    height: 60px;
    margin-right: 20px;
    border: 3px solid #fff;
    border-radius: 50px;
    cursor: pointer;
}

.img2 {
    width: 100px;
    height: 100px;
    margin-top: -50px;
}

.custom-truncate,
.one-truncate {
    display: -webkit-box;
    -webkit-box-orient: vertical;
    overflow: hidden;
}

.custom-truncate {
    -webkit-line-clamp: 2;
}

.prom {
    padding-right: 60px;
}

.one-truncate {
    -webkit-line-clamp: 1;
}

.bg-cover {
    width: 100%;
    min-height: 25vh;
    border-radius: 0px;
    border-bottom-left-radius: 10px;
    border-bottom-right-radius: 10px;
    background-position: center;
    background-size: cover;
    color: #fff;
    background-image: linear-gradient(rgba(0, 0, 0, 0.8), rgba(0, 0, 0, 0.8)), url('assets/themes/uac.png');
}

.logout-btn {
    width: 100%;
    bottom: 0;
    text-align: center;
    position: absolute;
    display: flex;
    justify-content: center;
    align-items: center;
    padding: 10px 0;
    background: #3498DB;
    color: white;
}

.bg-asside {
    background: #2D3E50;
    color: #fff;
}

.bg-asside i,
.bg-asside a span {
    color: #fff;
}

.t-bg-success {
    background: #007bff;
    color: #fff;
}

a {
    cursor: pointer;
}

.star {
    font-size: 1.2rem;
    color: red;
}

#base-style_length,
#base-style_filter,
#base-style_paginate,
#base-style_info {
    display: none;
}

.input-search {
    display: flex;
    /* width:100%; */
    padding: 4px;
    font-size: 1rem;
    /* font-weight:400; */
    line-height: 1.5;
    color: var(--bs-body-color);
    -webkit-appearance: none;
    -moz-appearance: none;
    appearance: none;
    background-color: var(--bs-body-bg);
    background-clip: padding-box;
    border: 1px solid #CED4DA;
    border-radius: var(--bs-border-radius);
    justify-content: center;
    align-items: center;
    transition: border-color .15s ease-in-out, box-shadow .15s ease-in-out
}

.input-search:focus {
    border: 1px solid #1890FF;
}

.input-search input {
    border: 0;
    width: 100%;
    padding-left: 6px;
}

.input-search input:focus,
.input-search .form-control {
    border: 0;
    outline: 0;
}

.loading {
    border: 3px solid #f3f3f3;
    border-top: 3px solid #3498db;
    border-radius: 50%;
    width: 16px;
    height: 16px;
    animation: spin 1s linear infinite;
    display: inline-block;
    margin-right: 5px;
}

@keyframes spin {
    0% {
        transform: rotate(0deg);
    }

    100% {
        transform: rotate(360deg);
    }
}

</style>