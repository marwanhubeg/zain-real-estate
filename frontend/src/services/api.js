import axios from 'axios';

const API_URL = import.meta.env.VITE_API_URL || 'http://127.0.0.1:8000/api/v1';

const api = axios.create({
  baseURL: API_URL,
  headers: {
    'Content-Type': 'application/json',
    'Accept': 'application/json',
  },
  timeout: 10000,
});

// Request interceptor
api.interceptors.request.use(
  (config) => {
    console.log('📡 Request:', config.method.toUpperCase(), config.url);
    return config;
  },
  (error) => Promise.reject(error)
);

// Response interceptor
api.interceptors.response.use(
  (response) => {
    console.log('✅ Response:', response.status, response.config.url);
    return response;
  },
  (error) => {
    console.error('❌ Error:', error.response?.status, error.config?.url);
    return Promise.reject(error);
  }
);

// ========== التصنيفات ==========
export const getCategories = () => api.get('/categories');
export const getCategory = (slug) => api.get(`/categories/${slug}`);
export const getPropertiesByCategory = (slug) => api.get(`/categories/${slug}/properties`);

// ========== العقارات ==========
export const getProperties = (params) => api.get('/properties', { params });
export const getProperty = (slug) => api.get(`/properties/${slug}`);
export const getFeaturedProperties = () => api.get('/properties/featured');

// ========== المواقع ==========
export const getLocations = () => api.get('/locations');
export const getLocation = (id) => api.get(`/locations/${id}`);

// ========== المزايا ==========
export const getAmenities = () => api.get('/amenities');

// ========== المصادقة (✅ تم إضافتها) ==========
export const register = (data) => api.post('/auth/register', data);
export const login = (data) => api.post('/auth/login', data);
export const logout = () => api.post('/auth/logout');
export const getUser = () => api.get('/user');
export const updateProfile = (data) => api.put('/user', data);
export const updateAvatar = (formData) => api.post('/user/avatar', formData, {
  headers: { 'Content-Type': 'multipart/form-data' }
});

// ========== الذكاء الاصطناعي ==========
export const getPersonalizedRecommendations = (limit) => 
  api.get('/recommendations/personalized', { params: { limit } });
export const getTrendingProperties = (limit) => 
  api.get('/recommendations/trending', { params: { limit } });
export const getSimilarProperties = (propertyId, limit) => 
  api.get(`/recommendations/similar/${propertyId}`, { params: { limit } });
export const getRecommendationsByBudget = (budget, purpose, limit) => 
  api.get('/recommendations/budget', { params: { budget, purpose, limit } });
export const getPricePredictions = (locationId, months) => 
  api.get('/predictions/prices', { params: { location_id: locationId, months } });
export const getDemandPredictions = (locationId) => 
  api.get('/predictions/demand', { params: { location_id: locationId } });

export default api;
