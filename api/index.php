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
        const canvas = document.getElementById('three-canvas');
        const scene = new THREE.Scene();
        const camera = new THREE.PerspectiveCamera(75, window.innerWidth / window.innerHeight, 0.1, 1000);
        const renderer = new THREE.WebGLRenderer({ canvas });
        renderer.setSize(window.innerWidth, window.innerHeight);

        // Luci
        const ambientLight = new THREE.AmbientLight(0xffffff, 0.5);
        scene.add(ambientLight);
        const pointLight = new THREE.PointLight(0xffffff, 1);
        pointLight.position.set(10, 10, 10);
        scene.add(pointLight);

        // Materiale per gli oggetti
        const material = new THREE.MeshStandardMaterial({ color: 0x00ff00 });

        // Creare tre oggetti
        const objects = [];
        const geometry = new THREE.SphereGeometry(1, 32, 32);
        for (let i = 0; i < 3; i++) {
            const sphere = new THREE.Mesh(geometry, material);
            sphere.position.set(i * 5 - 5, 0, 0);
            objects.push(sphere);
            scene.add(sphere);
        }

        // Canali tra gli oggetti
        const channelMaterial = new THREE.MeshBasicMaterial({ color: 0xffa500, transparent: true, opacity: 0.8 });
        const cylinderGeometry = new THREE.CylinderGeometry(0.1, 0.1, 5, 32);
        const channels = [];
        for (let i = 0; i < objects.length; i++) {
            const cylinder = new THREE.Mesh(cylinderGeometry, channelMaterial);
            cylinder.position.set((i - 0.5) * 5, 0, 0);
            cylinder.rotation.z = Math.PI / 2;
            channels.push(cylinder);
            scene.add(cylinder);
        }

        // Animazioni
        function animate() {
            requestAnimationFrame(animate);
            objects.forEach((obj, idx) => {
                obj.rotation.y += 0.01; // Rotazione
                obj.rotation.x += 0.01;
            });
            renderer.render(scene, camera);
        }

        // Imposta la camera e i controlli
        camera.position.z = 10;
        new OrbitControls(camera, renderer.domElement);

        animate();

        // Gestione del ridimensionamento
        window.addEventListener('resize', () => {
            camera.aspect = window.innerWidth / window.innerHeight;
            camera.updateProjectionMatrix();
            renderer.setSize(window.innerWidth, window.innerHeight);
        });
    </script>
</body>
</html>
