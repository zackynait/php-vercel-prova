<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Interactive 3D Scene with Car</title>
    <style>
        body { margin: 0; overflow: hidden; }
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
        camera.position.set(0, 5, 20); // Spostiamo la camera per avere una buona visione della scena

        // Aggiungi luci
        const ambientLight = new THREE.AmbientLight(0xffffff, 0.5);
        scene.add(ambientLight);
        const pointLight = new THREE.PointLight(0xffffff, 1);
        pointLight.position.set(0, 10, 10);
        scene.add(pointLight);

        // Oggetti rotanti
        const objects = [];
        const geometry = new THREE.BoxGeometry(2, 2, 2);
        const material = new THREE.MeshStandardMaterial({ color: 0x00ff00 });

        for (let i = 0; i < 3; i++) {
            const object = new THREE.Mesh(geometry, material);
            object.position.set(i * 5 - 5, 0, 0);
            scene.add(object);
            objects.push(object);
        }

        // Sfondo stellato
        const starGeometry = new THREE.BufferGeometry();
        const starCount = 500;
        const starPositions = new Float32Array(starCount * 3);

        for (let i = 0; i < starCount; i++) {
            starPositions[i * 3] = (Math.random() - 0.5) * 100;
            starPositions[i * 3 + 1] = (Math.random() - 0.5) * 100;
            starPositions[i * 3 + 2] = (Math.random() - 0.5) * 100;
        }

        starGeometry.setAttribute('position', new THREE.BufferAttribute(starPositions, 3));
        const starMaterial = new THREE.PointsMaterial({ color: 0xffffff, size: 0.5 });
        const stars = new THREE.Points(starGeometry, starMaterial);
        scene.add(stars);

        // Porta
        const doorGeometry = new THREE.BoxGeometry(5, 10, 1);
        const doorMaterial = new THREE.MeshStandardMaterial({ color: 0x8b4513 });
        const door = new THREE.Mesh(doorGeometry, doorMaterial);
        door.position.set(0, 0, -50);
        scene.add(door);

        let doorOpen = false;

        // Clicca per aprire la porta
        window.addEventListener('click', (event) => {
            const { clientX, clientY } = event;
            const width = window.innerWidth;
            const height = window.innerHeight;

            const x = (clientX / width) * 2 - 1;
            const y = -(clientY / height) * 2 + 1;

            const raycaster = new THREE.Raycaster();
            raycaster.setFromCamera({ x, y }, camera);
            const intersects = raycaster.intersectObject(door);

            if (intersects.length > 0 && !doorOpen) {
                doorOpen = true;
                door.rotation.y += Math.PI / 2;
            }
        });

        // Caricamento della macchina
        const carGeometry = new THREE.BoxGeometry(2, 1, 4);
        const carMaterial = new THREE.MeshStandardMaterial({ color: 0xff0000 });
        const car = new THREE.Mesh(carGeometry, carMaterial);
        car.position.set(0, 0.5, 0);
        scene.add(car);

        // Movimento della macchina
        const carSpeed = 0.2;
        let carDirection = new THREE.Vector3(0, 0, 0); // Direzione iniziale della macchina

        // Ascolta gli eventi delle frecce direzionali
        window.addEventListener('keydown', (event) => {
            if (event.key === 'ArrowUp') {
                carDirection.z = -carSpeed; // Muove in avanti
            } else if (event.key === 'ArrowDown') {
                carDirection.z = carSpeed; // Muove indietro
            } else if (event.key === 'ArrowLeft') {
                carDirection.x = -carSpeed; // Muove a sinistra
            } else if (event.key === 'ArrowRight') {
                carDirection.x = carSpeed; // Muove a destra
            }
        });

        window.addEventListener('keyup', (event) => {
            if (event.key === 'ArrowUp' || event.key === 'ArrowDown') {
                carDirection.z = 0; // Ferma il movimento in Z
            }
            if (event.key === 'ArrowLeft' || event.key === 'ArrowRight') {
                carDirection.x = 0; // Ferma il movimento in X
            }
        });

        // Aggiungi OrbitControls per la rotazione con il mouse
        const controls = new OrbitControls(camera, renderer.domElement);
        controls.enableDamping = true;
        controls.dampingFactor = 0.25;
        controls.screenSpacePanning = false;
        controls.maxPolarAngle = Math.PI / 2;

        // Animazione
        const animate = () => {
            requestAnimationFrame(animate);

            // Movimento della macchina
            car.position.add(carDirection);

            // Movimento delle stelle
            const starPositions = stars.geometry.attributes.position.array;
            for (let i = 0; i < starPositions.length; i += 3) {
                starPositions[i + 1] -= 0.02;
                if (starPositions[i + 1] < -50) starPositions[i + 1] = 50;
            }
            stars.geometry.attributes.position.needsUpdate = true;

            // Aggiorna i controlli
            controls.update();

            // Rendering della scena
            renderer.render(scene, camera);
        };

        animate();

        // Adatta il canvas alla finestra
        window.addEventListener('resize', () => {
            camera.aspect = window.innerWidth / window.innerHeight;
            camera.updateProjectionMatrix();
            renderer.setSize(window.innerWidth, window.innerHeight);
        });
    </script>
</body>
</html>
