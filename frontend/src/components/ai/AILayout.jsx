import React from 'react';
import { Outlet, NavLink } from 'react-router-dom';
import { FaChartLine, FaHeart, FaRobot } from 'react-icons/fa';

const AILayout = () => {
  return (
    <div className="min-h-screen bg-gray-50">
      <div className="bg-white shadow-md">
        <div className="container mx-auto px-4">
          <div className="flex items-center gap-8 py-4">
            <NavLink
              to="/predictions"
              className={({ isActive }) =>
                `flex items-center gap-2 px-4 py-2 rounded-lg transition-colors ${
                  isActive ? 'bg-primary-600 text-white' : 'hover:bg-gray-100'
                }`
              }
            >
              <FaChartLine />
              <span>التنبؤات</span>
            </NavLink>
            <NavLink
              to="/recommendations"
              className={({ isActive }) =>
                `flex items-center gap-2 px-4 py-2 rounded-lg transition-colors ${
                  isActive ? 'bg-primary-600 text-white' : 'hover:bg-gray-100'
                }`
              }
            >
              <FaRobot />
              <span>التوصيات الذكية</span>
            </NavLink>
          </div>
        </div>
      </div>
      <Outlet />
    </div>
  );
};

export default AILayout;
