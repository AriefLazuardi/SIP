import defaultTheme from "tailwindcss/defaultTheme";
import forms from "@tailwindcss/forms";

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        "./vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php",
        "./storage/framework/views/*.php",
        "./resources/views/**/*.blade.php",
    ],

    theme: {
        extend: {
            fontFamily: {
                sans: ["DM Sans", ...defaultTheme.fontFamily.sans],
            },
            colors: {
                customColor: "#2b2b2b",
                dangerColor: "#E6143E",
                whiteColor: "#f1f1f1",
                baseColor: "#FDFDFD",
                primaryColor: "#00b21d",
                darkPrimaryColor: "#007713",
                lightColor: "#00c620",
                yellowColor: "#F4CE00",
                orangeColor: "#FF8C00",
                blueColor: "#1E90FF",
                cyanColor: "#00E8E8",
            },
        },
    },

    plugins: [forms],
};
