// Import the functions you need from the SDKs you need
import { initializeApp } from "firebase/app";
import { getAnalytics } from "firebase/analytics";
// TODO: Add SDKs for Firebase products that you want to use
// https://firebase.google.com/docs/web/setup#available-libraries

// Your web app's Firebase configuration
// For Firebase JS SDK v7.20.0 and later, measurementId is optional
const firebaseConfig = {
  apiKey: "AIzaSyBkNG0dzulbr7wh5HO-DAYgdKO-nc3Nvbs",
  authDomain: "cashtime-5ec62.firebaseapp.com",
  projectId: "cashtime-5ec62",
  storageBucket: "cashtime-5ec62.appspot.com",
  messagingSenderId: "812060100243",
  appId: "1:812060100243:web:bc58fee1791750e9752f66",
  measurementId: "G-Q5QR4P4SCZ"
};

// Initialize Firebase
const app = initializeApp(firebaseConfig);
const analytics = getAnalytics(app);