<script src="https://www.gstatic.com/firebasejs/7.14.0/firebase-app.js"></script>
<script src="https://www.gstatic.com/firebasejs/7.14.0/firebase-auth.js"></script>
<script src="https://www.gstatic.com/firebasejs/7.14.0/firebase-firestore.js"></script>
<script>
    // Initialize Firebase
    var firebaseConfig = {
  apiKey: "AIzaSyAufimqkLWvPtzEc-g-gsxlPN96fR8m8Tc",
  authDomain: "kopi-sinarindo.firebaseapp.com",
  projectId: "kopi-sinarindo",
  storageBucket: "kopi-sinarindo.appspot.com",
  messagingSenderId: "507940483635",
  appId: "1:507940483635:web:fcbb34eb003eef453d6946",
  measurementId: "G-NLEC59ZXP5"
};
    firebase.initializeApp(config);
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
