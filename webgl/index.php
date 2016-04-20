<!DOCTYPE html>
<!--
To change this license header, choose License Headers in Project Properties.
To change this template file, choose Tools | Templates
and open the template in the editor.
-->
<html>
    <head>
        <meta charset="UTF-8">
        <link href="css/menu.css" rel="stylesheet" type="text/css">
        <link href="css/mystyle.css" rel="stylesheet" type="text/css"/>
        <title>Административные правонарушения</title>
        <script type="text/javascript" src="webgl/scripts/glMatrix-0.9.5.min.js"></script>
        <script type="text/javascript" src="webgl/scripts/webgl-utils.js"></script>

        <script id="per-vertex-lighting-fs" type="x-shader/x-fragment">
            precision mediump float;

            varying vec2 vTextureCoord;
            varying vec3 vLightWeighting;

            uniform bool uUseTextures;

            uniform sampler2D uSampler;

            void main(void) {
            vec4 fragmentColor;
            if (uUseTextures) {
            fragmentColor = texture2D(uSampler, vec2(vTextureCoord.s, vTextureCoord.t));
            } else {
            fragmentColor = vec4(1.0, 1.0, 1.0, 1.0);
            }
            gl_FragColor = vec4(fragmentColor.rgb * vLightWeighting, fragmentColor.a);
            }
        </script>

        <script id="per-vertex-lighting-vs" type="x-shader/x-vertex">
            attribute vec3 aVertexPosition;
            attribute vec3 aVertexNormal;
            attribute vec2 aTextureCoord;

            uniform mat4 uMVMatrix;
            uniform mat4 uPMatrix;
            uniform mat3 uNMatrix;

            uniform vec3 uAmbientColor;

            uniform vec3 uPointLightingLocation;
            uniform vec3 uPointLightingColor;

            uniform bool uUseLighting;

            varying vec2 vTextureCoord;
            varying vec3 vLightWeighting;

            void main(void) {
            vec4 mvPosition = uMVMatrix * vec4(aVertexPosition, 1.0);
            gl_Position = uPMatrix * mvPosition;
            vTextureCoord = aTextureCoord;

            if (!uUseLighting) {
            vLightWeighting = vec3(1.0, 1.0, 1.0);
            } else {
            vec3 lightDirection = normalize(uPointLightingLocation - mvPosition.xyz);

            vec3 transformedNormal = uNMatrix * aVertexNormal;
            float directionalLightWeighting = max(dot(transformedNormal, lightDirection), 0.0);
            vLightWeighting = uAmbientColor + uPointLightingColor * directionalLightWeighting;
            }
            }
        </script>


        <script id="per-fragment-lighting-fs" type="x-shader/x-fragment">
            precision mediump float;

            varying vec2 vTextureCoord;
            varying vec3 vTransformedNormal;
            varying vec4 vPosition;

            uniform bool uUseLighting;
            uniform bool uUseTextures;

            uniform vec3 uAmbientColor;

            uniform vec3 uPointLightingLocation;
            uniform vec3 uPointLightingColor;

            uniform sampler2D uSampler;


            void main(void) {
            vec3 lightWeighting;
            if (!uUseLighting) {
            lightWeighting = vec3(1.0, 1.0, 1.0);
            } else {
            vec3 lightDirection = normalize(uPointLightingLocation - vPosition.xyz);

            float directionalLightWeighting = max(dot(normalize(vTransformedNormal), lightDirection), 0.0);
            lightWeighting = uAmbientColor + uPointLightingColor * directionalLightWeighting;
            }

            vec4 fragmentColor;
            if (uUseTextures) {
            fragmentColor = texture2D(uSampler, vec2(vTextureCoord.s, vTextureCoord.t));
            } else {
            fragmentColor = vec4(1.0, 1.0, 1.0, 1.0);
            }
            gl_FragColor = vec4(fragmentColor.rgb * lightWeighting, fragmentColor.a);
            }
        </script>

        <script id="per-fragment-lighting-vs" type="x-shader/x-vertex">
            attribute vec3 aVertexPosition;
            attribute vec3 aVertexNormal;
            attribute vec2 aTextureCoord;

            uniform mat4 uMVMatrix;
            uniform mat4 uPMatrix;
            uniform mat3 uNMatrix;

            varying vec2 vTextureCoord;
            varying vec3 vTransformedNormal;
            varying vec4 vPosition;


            void main(void) {
            vPosition = uMVMatrix * vec4(aVertexPosition, 1.0);
            gl_Position = uPMatrix * vPosition;
            vTextureCoord = aTextureCoord;
            vTransformedNormal = uNMatrix * aVertexNormal;
            }
        </script>


        <script type="text/javascript" src="webgl/scripts/myWebGL.js"></script>

    </head>
    <body  onload="webGLStart();">
        <div id="sitebranding" align="center">
            <h1>Административные правонарушения</h1>
        </div>
        <div id="mainmenu">
            <ul>
                <li><a href="#">О проекте</a></li>
                <li><a href="#">Контакты</a></li>
                <li><a href="login.php">Вход</a></li>
            </ul><!-- Конец списка -->
        </div><!-- Конец блока #mainmenu -->
        <div align="center"  >
            <canvas  id="canvas1" style="border: none;" width="1000" height="500"></canvas>
            <br/>
            <input  type="button"   value="Свет"  id="lighting" 
                    onclick="changeLighting()" style='display: none;'/><br/>
            <input type="button"  value="Цвет" id="per-fragment"
                   onclick="changeColor()" style='display: none;'/><br/>
            <input type="button"  value="Текстура" id="textures"
                   onclick="changeTexture()" style='display: none;'/><br/>
        </div>
    </body>
</html>
