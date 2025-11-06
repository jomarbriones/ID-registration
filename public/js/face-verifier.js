/**
 * Lightweight wrapper around face-api.js that prepares models and produces
 * 128-dimension embeddings for face comparison on the client.
 *
 * Requirements:
 *   1. Place the face-api.js weight files under /face-models (or override via
 *      window.faceVerifierConfig.modelPath before this script runs).
 *   2. Ensure this script is loaded on pages that should capture embeddings.
 *
 * The script exposes a single global helper: window.FaceVerifier.
 */
(function () {
  const FACE_API_SRC =
    (window.faceVerifierConfig && window.faceVerifierConfig.scriptSrc) ||
    'https://cdn.jsdelivr.net/npm/face-api.js@0.22.2/dist/face-api.min.js';

  function resolveModelPath(rawPath) {
    const fallback = rawPath || 'face-models';
    try {
      // new URL handles absolute or relative (including app subdirectories)
      const url = new URL(fallback, window.location.href);
      return url.toString().replace(/\/$/, '');
    } catch (_) {
      return fallback.replace(/\/$/, '');
    }
  }

  const MODEL_PATH = resolveModelPath(
    window.faceVerifierConfig && window.faceVerifierConfig.modelPath
  );

  const state = {
    faceApiLoading: false,
    faceApiReady: typeof window !== 'undefined' && !!window.faceapi,
    modelsLoading: false,
    modelsReady: false,
  };

  function loadScriptOnce(url) {
    return new Promise((resolve, reject) => {
      if (document.querySelector(`script[src="${url}"]`)) {
        return resolve();
      }
      const script = document.createElement('script');
      script.src = url;
      script.async = true;
      script.onload = () => resolve();
      script.onerror = () =>
        reject(new Error(`Failed to load Face API bundle: ${url}`));
      document.head.appendChild(script);
    });
  }

  async function ensureFaceApi() {
    if (state.faceApiReady) return;
    if (state.faceApiLoading) {
      await new Promise((resolve) => {
        const id = setInterval(() => {
          if (state.faceApiReady) {
            clearInterval(id);
            resolve();
          }
        }, 40);
      });
      return;
    }

    state.faceApiLoading = true;
    await loadScriptOnce(FACE_API_SRC);
    if (!window.faceapi) {
      throw new Error('faceapi global is unavailable after script load.');
    }
    state.faceApiReady = true;
  }

  async function ensureModels() {
    if (state.modelsReady) return;
    if (state.modelsLoading) {
      await new Promise((resolve) => {
        const id = setInterval(() => {
          if (state.modelsReady) {
            clearInterval(id);
            resolve();
          }
        }, 40);
      });
      return;
    }

    state.modelsLoading = true;
    await ensureFaceApi();
    const { nets } = window.faceapi;
    await Promise.all([
      nets.ssdMobilenetv1.loadFromUri(MODEL_PATH),
      nets.faceLandmark68Net.loadFromUri(MODEL_PATH),
      nets.faceRecognitionNet.loadFromUri(MODEL_PATH),
    ]);
    state.modelsReady = true;
    state.modelsLoading = false;
  }

  function getFaceApi() {
    if (!window.faceapi) {
      throw new Error('faceapi not initialised.');
    }
    return window.faceapi;
  }

  function asImage(file) {
    return getFaceApi().bufferToImage(file);
  }

  function qualityFromBox(box) {
    if (!box) return 0;
    const size = Math.min(box.width, box.height);
    if (size >= 400) return 1;
    if (size >= 220) return 0.6;
    return 0.3;
  }

  async function embedFile(file, opts = {}) {
    if (!file) {
      throw new Error('No file supplied for embedding.');
    }

    await ensureModels();
    const faceapi = getFaceApi();
    const img = await asImage(file);
    try {
      const detection = await faceapi
        .detectSingleFace(
          img,
          new faceapi.SsdMobilenetv1Options({
            minConfidence: opts.minConfidence ?? 0.5,
          })
        )
        .withFaceLandmarks()
        .withFaceDescriptor();

      if (!detection) {
        throw new Error('face-not-found');
      }

      const vector = Array.from(detection.descriptor || []);
      if (!vector.length) {
        throw new Error('descriptor-empty');
      }

      return {
        vector,
        overview: {
          confidence: detection.detection.score,
          box: detection.detection.box,
          quality: qualityFromBox(detection.detection.box),
        },
      };
    } finally {
      if (img && typeof img.remove === 'function') {
        img.remove();
      }
    }
  }

  const FaceVerifier = {
    async prepare() {
      await ensureModels();
      return { modelsReady: state.modelsReady };
    },
    async embedFile(file, opts) {
      return embedFile(file, opts);
    },
    isReady() {
      return state.modelsReady;
    },
  };

  window.FaceVerifier = FaceVerifier;
})();
