<script src="https://www.gstatic.com/firebasejs/7.14.0/firebase-app.js"></script>
<script src="https://www.gstatic.com/firebasejs/7.14.0/firebase-auth.js"></script>
<script src="https://www.gstatic.com/firebasejs/7.14.0/firebase-firestore.js"></script>
<script type="module">
    // Import the functions you need from the SDKs you need
    import { initializeApp } from "https://www.gstatic.com/firebasejs/10.3.0/firebase-app.js";
    import { getAnalytics } from "https://www.gstatic.com/firebasejs/10.3.0/firebase-analytics.js";
    // TODO: Add SDKs for Firebase products that you want to use
    // https://firebase.google.com/docs/web/setup#available-libraries
  
    // Your web app's Firebase configuration
    // For Firebase JS SDK v7.20.0 and later, measurementId is optional
    const firebaseConfig = {
      apiKey: "AIzaSyAufimqkLWvPtzEc-g-gsxlPN96fR8m8Tc",
      authDomain: "kopi-sinarindo.firebaseapp.com",
      projectId: "kopi-sinarindo",
      storageBucket: "kopi-sinarindo.appspot.com",
      messagingSenderId: "507940483635",
      appId: "1:507940483635:web:fcbb34eb003eef453d6946",
      measurementId: "G-NLEC59ZXP5"
    };
  
    // Initialize Firebase
    const app = initializeApp(firebaseConfig);
    const analytics = getAnalytics(app);
    
    var facebookProvider = new firebase.auth.FacebookAuthProvider();
    var googleProvider = new firebase.auth.GoogleAuthProvider();
    var facebookCallbackLink = '/login/facebook/callback';
    var googleCallbackLink = '/login/google/callback';
    async function socialSignin(provider) {
        var socialProvider = null;
        if (provider == "facebook") {
            socialProvider = facebookProvider;
            document.getElementById('social-login-form').action = facebookCallbackLink;
        } else if (provider == "google") {
            socialProvider = googleProvider;
            document.getElementById('social-login-form').action = googleCallbackLink;
        } else {
            return;
        }
        firebase.auth().signInWithPopup(socialProvider).then(function(result) {
            result.user.getIdToken().then(function(result) {
                document.getElementById('social-login-tokenId').value = result;
                document.getElementById('social-login-form').submit();
            });
        }).catch(function(error) {
            // do error handling
            console.log(error);
        });
    }
</script>
