<?php

// List of meal images in order
$images = [
    'meals/3pFOnrP6yZtfRYNRW2xaq2CyGlBb2VG8hJCTQeQ1.jpg',  // Ugali Maharage
    'meals/4hp1mbOjr1R7WHzdUVhehmRqM07rFkDedzmYrH2v.jpg',  // Wali Maharage
    'meals/6D8WqWTUBQEYqYPi8zKlgzdXDBIqP14ZoiYhdnHf.jpg',  // Pilau
    'meals/880QOj6lOFASW6PRZrTWGmvvlYMgvAkhpI5Vb7ca.jpg',  // Samaki wa Kupaka
    'meals/9EAPEXWMDYIlwHx9e83WQzLAmnJcYh2OLxpvftxn.jpg',  // Mchuzi wa Kuku
    'meals/AccHXktiLI0Go5O6phTktiE1If51pFs5wFCyiRve.jpg',  // Beef Stew
    'meals/aZ8zkdt1ZgskFpQswxAnirHpP622J5Eh5avcjqvb.jpg',  // Vegetable Curry
    'meals/C4oiMkZXiWuN1lxy2afr8bCmgwqXcyMBt53U6ruG.jpg',  // Kisamvu na Nyama
    'meals/HaJyQwGUauQM57x4AZBY9b1GYvTWNRquneD5ayTO.jpg',  // Mchemsho
    'meals/JcEMzhEgPNIjtlLqZUOmqwdTByP6LLwFJtH6DcEg.jpg',  // Chips Mayai
    'meals/pWmHRcofYaigWvhmXNi5kBzp2JPboMIZ4v8od1qd.jpg',  // Chapati
    'meals/QIAQVDvXmW4OpYiXABW0GtHXhsD0Ixh9716KsmQv.jpg',  // Mandazi
    'meals/RmaoNtYUvd7KgwRnd92fZB5gfLlQE54l7h4aAI2p.jpg',  // Chai
    'meals/srDtrvg8AuWnHADFAMvYtVrqjJ01sbuHxSHWzeCr.jpg',  // Fresh Juice
    'meals/Ti28y0TRE7PcFIptykV6RHWOFPcuMRxm8nG6zVjg.jpg',  // Coffee
    'meals/W4Rdk8O7pVtotk4BdECTnN6iabPUaoh69ou30jFD.jpg',  // Katogo
    'meals/XGeUXmfeU0upk80dXW1d4AZH1Q6F5BCaVim618cc.jpg',  // Matoke
    'meals/Z74RmsNRQ87iItvJeeFudR8sI8GuAYykDwUfATIr.jpg',  // Supu ya Ndizi
];

echo "Image URLs for meals:\n";
foreach ($images as $index => $image) {
    echo ($index + 1) . ". $image\n";
}
