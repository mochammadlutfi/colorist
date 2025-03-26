import { useAppBaseStore } from "@/Stores/base";
import currency from 'currency.js';
import dayjs from 'dayjs';

export const useFormatter = () => {

    const formatDate = (value) => {
        const appBase = useAppBaseStore();

        const dateFormat = appBase.app.date_format || 'YYYY-MM-DD'; 
        const timeFormat = appBase.app.time_format || 'HH:mm:ss';
        
        const isTimeIncluded = dayjs(value).hour() || dayjs(value).minute() || dayjs(value).second();
        const formatString = isTimeIncluded ? `${dateFormat} ${timeFormat}` : dateFormat;
    
        return dayjs(value).format(formatString);
    }
    

    const formatCurrency = (value) => {
        const appBase = useAppBaseStore();
        const separator = ',';
        const decimal =  '.';
        const precision = 0;
        const symbol = 'Rp';

        // Format nilai dengan currency.js
        return currency(value ?? 0, {
            increment: .5,
            symbol: symbol,
            separator: separator, // Pemisah ribuan
            decimal: decimal, // Pemisah desimal
            precision: precision, // Jumlah angka desimal
        }).format();
    };
    
    /**
     * Mengkonversi object menjadi FormData
     * @param {Object} object - Object yang akan dikonversi
     * @returns {FormData}
     */
    const objectToFormData = (data) => {
        const formData = new FormData();
        for (const key in data) {
            const value = data[key];

            // Jika nilai adalah array
            if (Array.isArray(value)) {
                // Jika array berisi file
                if (key === 'images' && value.every(item => item instanceof File)) {
                    value.forEach((file, index) => {
                        formData.append(`${key}[${index}]`, file);
                    });
                } else {
                    // Jika array tidak berisi file, ubah menjadi string JSON
                    formData.append(key, JSON.stringify(value));
                }
            } else if (value instanceof File) {
                // Jika nilai adalah file
                formData.append(key, value);
            } else if (typeof value === 'object' && value !== null) {
                // Jika nilai adalah objek (non-array), ubah ke string JSON
                formData.append(key, JSON.stringify(value));
            } else if (value !== undefined && value !== null && value !== "null") {
                // Tambahkan nilai primitif (string, number, boolean)
                formData.append(key, value);
            }else if(value == null || value == "null"){
                formData.append(key, '');
            }
        }
        return formData;

    };

    return {
        formatDate, objectToFormData, formatCurrency
    };
};