import React, { useState, useEffect } from 'react';
import { getPricePredictions, getDemandPredictions } from '../../services/ai';
import { FaChartLine, FaCalendar, FaMapMarkerAlt, FaArrowUp, FaArrowDown, FaMinus } from 'react-icons/fa';
import {
  LineChart, Line, BarChart, Bar, XAxis, YAxis, CartesianGrid,
  Tooltip, Legend, ResponsiveContainer
} from 'recharts';

const PredictionsPage = () => {
  const [pricePredictions, setPricePredictions] = useState(null);
  const [demandPredictions, setDemandPredictions] = useState(null);
  const [loading, setLoading] = useState(true);
  const [selectedLocation, setSelectedLocation] = useState('all');

  useEffect(() => {
    fetchPredictions();
  }, [selectedLocation]);

  const fetchPredictions = async () => {
    setLoading(true);
    try {
      const [priceRes, demandRes] = await Promise.all([
        getPricePredictions(selectedLocation === 'all' ? null : selectedLocation, 12),
        getDemandPredictions(selectedLocation === 'all' ? null : selectedLocation)
      ]);
      
      setPricePredictions(priceRes.data.data);
      setDemandPredictions(demandRes.data.data);
    } catch (error) {
      console.error('Error fetching predictions:', error);
    } finally {
      setLoading(false);
    }
  };

  const getTrendIcon = (trend) => {
    switch(trend) {
      case 'rising':
        return <FaArrowUp className="text-green-600" />;
      case 'falling':
        return <FaArrowDown className="text-red-600" />;
      default:
        return <FaMinus className="text-yellow-600" />;
    }
  };

  const getTrendText = (trend) => {
    switch(trend) {
      case 'rising':
        return 'ارتفاع متوقع';
      case 'falling':
        return 'انخفاض متوقع';
      default:
        return 'استقرار متوقع';
    }
  };

  // تحويل بيانات الطلب إلى صيغة مناسبة للرسم
  const demandChartData = demandPredictions 
    ? Object.values(demandPredictions).map((item, index) => ({
        month: item.month_name,
        demand: item.booking_count,
        level: item.demand_level
      }))
    : [];

  return (
    <div className="min-h-screen bg-gray-50 py-8">
      <div className="container mx-auto px-4">
        {/* Header */}
        <div className="mb-8">
          <h1 className="text-3xl font-bold mb-2">التنبؤات والتحليلات</h1>
          <p className="text-gray-600">تحليلات ذكية وتوقعات مستقبلية لسوق العقارات في الإسماعيلية</p>
        </div>

        {/* Location Filter */}
        <div className="bg-white rounded-xl shadow-md p-4 mb-6">
          <div className="flex items-center gap-4">
            <FaMapMarkerAlt className="text-primary-600" />
            <select
              value={selectedLocation}
              onChange={(e) => setSelectedLocation(e.target.value)}
              className="input w-64"
            >
              <option value="all">جميع المواقع</option>
              <option value="1">حي النخيل</option>
              <option value="2">الحي التجاري</option>
              <option value="3">كمبوند النخبة</option>
              <option value="4">الجامعة</option>
            </select>
          </div>
        </div>

        {loading ? (
          <div className="grid grid-cols-1 gap-6">
            {[1,2,3].map(i => (
              <div key={i} className="skeleton h-64 rounded-xl"></div>
            ))}
          </div>
        ) : (
          <>
            {/* Price Predictions */}
            {pricePredictions && (
              <div className="bg-white rounded-2xl shadow-lg p-6 mb-8">
                <div className="flex justify-between items-center mb-6">
                  <div className="flex items-center gap-3">
                    <div className="bg-blue-100 p-3 rounded-xl">
                      <FaChartLine className="text-blue-600 text-xl" />
                    </div>
                    <div>
                      <h2 className="text-2xl font-bold">تنبؤات الأسعار</h2>
                      <p className="text-gray-600">
                        السعر الحالي: {pricePredictions.current?.toLocaleString()} ج.م
                        <span className="mx-2">|</span>
                        الاتجاه: {getTrendIcon(pricePredictions.trend)} {getTrendText(pricePredictions.trend)}
                      </p>
                    </div>
                  </div>
                </div>

                {pricePredictions.predictions && pricePredictions.predictions.length > 0 && (
                  <div className="h-80">
                    <ResponsiveContainer width="100%" height="100%">
                      <LineChart
                        data={pricePredictions.predictions}
                        margin={{ top: 5, right: 30, left: 20, bottom: 5 }}
                      >
                        <CartesianGrid strokeDasharray="3 3" />
                        <XAxis dataKey="month" />
                        <YAxis />
                        <Tooltip />
                        <Legend />
                        <Line 
                          type="monotone" 
                          dataKey="predicted_price" 
                          name="السعر المتوقع" 
                          stroke="#8884d8" 
                          activeDot={{ r: 8 }} 
                        />
                      </LineChart>
                    </ResponsiveContainer>
                  </div>
                )}
              </div>
            )}

            {/* Demand Predictions */}
            {demandPredictions && demandChartData.length > 0 && (
              <div className="bg-white rounded-2xl shadow-lg p-6">
                <div className="flex items-center gap-3 mb-6">
                  <div className="bg-green-100 p-3 rounded-xl">
                    <FaCalendar className="text-green-600 text-xl" />
                  </div>
                  <div>
                    <h2 className="text-2xl font-bold">الطلب الموسمي</h2>
                    <p className="text-gray-600">توزيع الطلب على مدار العام</p>
                  </div>
                </div>

                <div className="h-80">
                  <ResponsiveContainer width="100%" height="100%">
                    <BarChart
                      data={demandChartData}
                      margin={{ top: 5, right: 30, left: 20, bottom: 5 }}
                    >
                      <CartesianGrid strokeDasharray="3 3" />
                      <XAxis dataKey="month" />
                      <YAxis />
                      <Tooltip />
                      <Legend />
                      <Bar dataKey="demand" name="عدد الحجوزات" fill="#8884d8" />
                    </BarChart>
                  </ResponsiveContainer>
                </div>
              </div>
            )}
          </>
        )}
      </div>
    </div>
  );
};

export default PredictionsPage;
