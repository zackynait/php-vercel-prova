<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Three.js Visualization</title>
    <style>
        body { margin: 0; }
        canvas { display: block; }
    </style>
</head>
<body>
    <canvas id="three-canvas"></canvas>
    <script type="importmap">
        {
            "imports": {
                "three": "https://unpkg.com/three@0.138.0/build/three.module.js",
                "OrbitControls": "https://unpkg.com/three@0.138.0/examples/jsm/controls/OrbitControls.js"
            }
        }
    </script>
    <script type="module">
       import * as THREE from 'three';
        import { OrbitControls } from 'OrbitControls';
        // Scena, Camera, Renderer
        const canvas = document.getElementById('three-canvas');
        const scene = new THREE.Scene();
        const camera = new THREE.PerspectiveCamera(75, window.innerWidth / window.innerHeight, 0.1, 1000);
        const renderer = new THREE.WebGLRenderer({ canvas });
        renderer.setSize(window.innerWidth, window.innerHeight);
        camera.position.z = 15;

        // Luci
        const ambientLight = new THREE.AmbientLight(0xffffff, 0.5);
        scene.add(ambientLight);
        const pointLight = new THREE.PointLight(0xffffff, 1);
        pointLight.position.set(10, 10, 10);
        scene.add(pointLight);

        // Materiali
        const blueMaterial = new THREE.MeshStandardMaterial({ color: 0x0000ff });
        const purpleMaterial = new THREE.MeshStandardMaterial({ color: 0x800080 });
        const channelMaterial = new THREE.MeshBasicMaterial({ color: 0xffa500, transparent: true, opacity: 0.5 });

        // Creare i prismi blu
        const bluePrisms = [];
        const prismGeometry = new THREE.CylinderGeometry(0.5, 0.5, 2, 6); // Prisma esagonale
        for (let i = 0; i < 3; i++) {
            const prism = new THREE.Mesh(prismGeometry, blueMaterial);
            prism.position.set(-5, i * 4 - 4, 0);
            bluePrisms.push(prism);
            scene.add(prism);
        }

        // Creare gli oggetti viola
        const purpleObjects = [];
        const sphereGeometry = new THREE.SphereGeometry(0.8, 32, 32);
        for (let i = 0; i < 3; i++) {
            const sphere = new THREE.Mesh(sphereGeometry, purpleMaterial);
            sphere.position.set(5, i * 4 - 4, 0);
            purpleObjects.push(sphere);
            scene.add(sphere);
        }

        // Creare i canali
        const channels = [];
        const channelGeometry = new THREE.CylinderGeometry(0.1, 0.1, 10, 32);
        for (let i = 0; i < 3; i++) {
            const channel = new THREE.Mesh(channelGeometry, channelMaterial);
            channel.position.set(0, i * 4 - 4, 0);
            channel.rotation.z = Math.PI / 2;
            channels.push(channel);
            scene.add(channel);
        }

        // Messaggi colorati nei canali
        const messageGeometry = new THREE.SphereGeometry(0.2, 16, 16);
        const messageMaterial = new THREE.MeshBasicMaterial({ color: 0xff0000 });
        const messages = channels.map(() => new THREE.Mesh(messageGeometry, messageMaterial));
        messages.forEach((msg, i) => {
            msg.position.copy(channels[i].position);
            scene.add(msg);
        });

        // Animazioni
        function animateMessages() {
            messages.forEach((msg, i) => {
                msg.position.x += 0.1; // Movimento lungo il canale
                if (msg.position.x > 5) msg.position.x = -5; // Ripristino
            });
        }

        function animate() {
            requestAnimationFrame(animate);

            // Rotazione dei prismi
            bluePrisms.forEach(prism => {
                prism.rotation.x += 0.02;
                prism.rotation.y += 0.02;
            });

            // Movimento dei messaggi
            animateMessages();

            // Render
            renderer.render(scene, camera);
        }

        // Controlli della camera
        new OrbitControls(camera, renderer.domElement);

        // Gestione del ridimensionamento
        window.addEventListener('resize', () => {
            camera.aspect = window.innerWidth / window.innerHeight;
            camera.updateProjectionMatrix();
            renderer.setSize(window.innerWidth, window.innerHeight);
        });

        animate();
    </script>
</body>
</html>
