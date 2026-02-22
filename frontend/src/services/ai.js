import api from './api';

// ========== نظام التوصيات ==========

/**
 * الحصول على توصيات مخصصة للمستخدم
 */
export const getPersonalizedRecommendations = (limit = 10) => {
  return api.get('/recommendations/personalized', { params: { limit } });
};

/**
 * الحصول على العقارات الأكثر طلباً
 */
export const getTrendingProperties = (limit = 10) => {
  return api.get('/recommendations/trending', { params: { limit } });
};

/**
 * الحصول على عقارات مشابهة
 */
export const getSimilarProperties = (propertyId, limit = 6) => {
  return api.get(`/recommendations/similar/${propertyId}`, { params: { limit } });
};

/**
 * توصيات حسب الميزانية
 */
export const getRecommendationsByBudget = (budget, purpose = 'sale', limit = 10) => {
  return api.get('/recommendations/budget', { params: { budget, purpose, limit } });
};

// ========== نظام التنبؤات ==========

/**
 * تنبؤات بأسعار العقارات
 */
export const getPricePredictions = (locationId = null, months = 12) => {
  return api.get('/predictions/prices', { params: { location_id: locationId, months } });
};

/**
 * تنبؤات الطلب الموسمي
 */
export const getDemandPredictions = (locationId = null) => {
  return api.get('/predictions/demand', { params: { location_id: locationId } });
};

export default {
  getPersonalizedRecommendations,
  getTrendingProperties,
  getSimilarProperties,
  getRecommendationsByBudget,
  getPricePredictions,
  getDemandPredictions
};
